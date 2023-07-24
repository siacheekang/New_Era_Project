<?php
session_start();
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Pages</title>
    <!-- Latest compiled and minified Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body style="background-color: #FFF8EA">


    <div class="container d-flex flex-column align-items-center">
        <a href="login.php"><img src="images/xsymbol.png" alt="logo"></a>
        <?php
        $action = isset($_GET['action']) ? $_GET['action'] : "";
        
        if ($action == 'successful') {
        echo "<div class='alert alert-success'>Account created successful.</div>";
        }
        ?>
        <div class="page-header my-3">
            <h2>Please sign in</h2>
        </div>

        <form class="d-flex flex-column align-items-center" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">

            <?php


            if ($_POST) {
                include 'config/database.php';
                $username = $_POST['username'];
                $pass = $_POST['password'];

                if (empty($_POST['username']) || empty($_POST['password'])) {
                    echo "<div class='alert alert-danger'>Username and password cannot be empty</div>";
                } else {
                    $query = "SELECT * FROM customer WHERE username=:username";
                    $stmt = $con->prepare($query);
                    $stmt->bindParam(":username", $username);
                    $stmt->execute();

                    $num = $stmt->rowCount();

                    if ($num > 0) {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            extract($row);
                            if (md5($pass) == $password) {
                                if ($account == "active") {
                                    header("Location: home.php");
                                    $_SESSION["login"] = true;
                                } else {
                                    echo "<div class='alert alert-danger'>Your Account is suspended</div>";
                                }
                            } else {
                                echo "<div class='alert alert-danger'>Incorrect password</div>";
                            }
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Invalid username</div>";
                    }
                }
            }
            ?>

            <div class="form-floating">
                <input type="username" name="username" class="form-control" id="floatingInput" placeholder="username">
                <label for="floatingInput">Username</label>

            </div>
            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password">
                <label for="floatingPassword">Password</label>
            </div>

            <div class="d-grid gap-2">
                <button class="btn btn-lg btn-primary" type="submit">Sign in</button>

            </div>
            <div class="mt-3">
                <p class="mb-0  text-center">Don't have an account? <a href="register.php" class="text-primary fw-bold">Sign
                        Up</a></p>
            </div>
            <p class="mt-3 mb-3">Copyright by Sia Chee Kang</p>
        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>