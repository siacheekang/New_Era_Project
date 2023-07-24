from flask import Flask, render_template, request, jsonify
import speech_recognition as sr
from chat import get_response


app = Flask(__name__)


@app.route("/", methods=["GET"])
def get_html():
    return render_template("design.html")


@app.route("/predict", methods=["POST"])
def predict():
    reply_msg = request.get_json().get("message")
    if not reply_msg:
        return jsonify({"error": "Invalid message"})

    # Check if the input is speech or text
    if reply_msg.startswith("SPEECH:"):
        # Perform speech-to-text conversion
        speech_text = convert_speech_to_text()

        # Pass the speech text to the chatbot for processing
        response = get_response(speech_text)
    else:
        # The input is text, pass it directly to the chatbot for processing
        response = get_response(reply_msg)

    message = {"answer": response}
    return jsonify(message)


def convert_speech_to_text():
    r = sr.Recognizer()
    with sr.Microphone() as source:
        print("Say something...")
        audio = r.listen(source)  # Listen for speech input

    try:
        # Use Google Speech Recognition to convert speech to text
        speech_text = r.recognize_google(audio)
        return speech_text
    except sr.UnknownValueError:
        return "Sorry, I couldn't understand the speech."
    except sr.RequestError as e:
        return f"Speech recognition error: {str(e)}"


if __name__ == "__main__":
    app.run(debug=True)
