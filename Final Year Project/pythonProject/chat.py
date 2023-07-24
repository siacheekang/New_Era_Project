import random
import json
import torch
from model import NeuralNet
from nltk_utils import bag_of_words, tokenize


device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')
with open('intents.json', 'r') as json_data:
    intents = json.load(json_data)


FILE = "data.pth"
data = torch.load(FILE)

input_size = data["input_size"]
hidden_size = data["hidden_size"]
output_size = data["output_size"]
all_words = data['all_words']
tags = data['tags']
model_state = data["model_state"]

model = NeuralNet(input_size, hidden_size, output_size).to(device)
model.load_state_dict(model_state)
with torch.no_grad():
    model.eval()


def get_response(input_msg):
    sentence = tokenize(input_msg)
    input_detail = bag_of_words(sentence, all_words)
    input_detail = input_detail.reshape(1, input_detail.shape[0])
    input_detail = torch.from_numpy(input_detail).to(device)

    output = model(input_detail)
    _, predicted = torch.max(output, dim=1)

    tag = tags[predicted.item()]

    probs = torch.softmax(output, dim=1)
    prob = probs[0][predicted.item()]
    if prob.item() > 0.75:
        for intent in intents['intents']:
            if tag == intent["tag"]:
                return random.choice(intent['responses'])

    return "sorry, I don't understand"


if __name__ == "__main__":
    print("Welcome to Chatbot! (type 'quit' to end program)")
    while True:
        user_input = input("You: ")
        if user_input == "quit":
            break

        resp = get_response(user_input)
        print(resp)