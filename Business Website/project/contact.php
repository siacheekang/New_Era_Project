<?php
session_start();
include 'config/usersign.php';
?>
<!DOCTYPE HTML>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contact</title>
  <!-- Latest compiled and minified Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body style="background-color: #FFF8EA">

  <?php
  include 'config/navbar.php';
  include 'config/database.php';
  ?>

  <form action="send.php" method="POST" enctype="multipart/form-data">
    <div class="container d-flex">
      <div class="col m-5">
        <h1 class="mb-3">Contact Us</h1>
        <p>"Aside from the obvious pink flamingo, Brandaffair captures the visitor's attention with three methods of communication. The map provides the exact location of the office, the "Meet Us" section includes a phone number and email for general inquiries, and the "Pitch Us" section includes a template that helps businesses submit their ideas directly to the company for consideration."</p>
        <ul>
          <li class="mb-2">Phone Number: 0178806566</li>
          <li class="mb-2">Email Address: siacheekang@gmail.com.my</li>
          <li>whatsapp Number: 60-178806566</li>
        </ul>
      </div>
      <div class="col m-5">
        <h1 class="mb-3">Get Touch</h1>
        <div class="form-floating mb-3">
          <input type="name" name="name" value="" class="form-control" id="floatingInput" placeholder="name">
          <label for="floatingInput">Name</label>
        </div>
        <div class="form-floating mb-3" >
          <input type="email" name="email" value="" class="form-control" id="floatingInput" placeholder="name@example.com">
          <label for="floatingInput">Email address</label>
        </div>
        <div class="form-floating">
          <textarea name="message" value="" class="form-control" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 140px"></textarea>
          <label for="floatingTextarea2">Comments</label>
        </div>
        <div class="col-12 mt-2 text-end">
          <button type="submit" name="send" class="btn btn-primary">Submit</button>
        </div>
      </div>
    </div>
  </form>

  



  <!-- end .container -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>