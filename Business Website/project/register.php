<!DOCTYPE HTML>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Account</title>
    <!-- Latest compiled and minified Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body style="background-color: #FFF8EA">


    <?php
    include 'config/database.php';
    ?>



    <!-- container -->
    <div class="container">
        <div class="page-header my-3">
            <h1>Create Customer</h1>
        </div>

        <?php
        if ($_POST) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $confirmpassword = $_POST['confpassword'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $dateofbirth = $_POST['dateofbirth'];
          
            

            $flag = 0;

            // new 'image' field
            $image = !empty($_FILES["image"]["name"])
            ? sha1_file($_FILES['image']['tmp_name']) . "-" . basename($_FILES["image"]["name"])
            : "";
             $image = htmlspecialchars(strip_tags($image));

            $requirement_error_messages = "";

            if ($username == "" || $password == "" || $firstname == ""  ||  $lastname == "" || $dateofbirth == "" ) {
                $requirement_error_messages.= "<div>You need to futfil all the basic requirement</div>";
                $flag = 1;
            }

            if (strlen($username) > 6) {
                if (strpos(trim($username), " ")) {
                    $requirement_error_messages.="<div>The username should not have blank</div>";
                    $flag =1;
                } 
            } else {
                $requirement_error_messages.= "<div>The username should include at least 6 character</div>";
                $flag = 1;
            }

            if (strlen($password) > 8) {
                if (preg_match('/[A-Z]/', $password)) {
                    if (preg_match('/[a-z]/', $password)) {
                        if (!preg_match('/[0-9]/', $password)) {
                            $requirement_error_messages.= "<div>The password should include number</div>";
                            $flag = 1;
                            
                            
                        }
                    } else {
                        $requirement_error_messages.= "<div>The password should include small letter</div>";
                        $flag = 1;
                    }
                } else {
                    $requirement_error_messages.= "<div>The password should include capital letter</div>";
                    $flag = 1;
                }
            } else {

                $requirement_error_messages.= "<div>The password should  include at least 8 character</div>";
                $flag = 1;
            }


            if ($password != $confirmpassword) {
                $requirement_error_messages.= "<div class='alert alert-danger'>Your password is not equall</div>";
                $flag = 1;
               
            }else{
                $password = md5($_POST['password']);
            }


          
            if($dateofbirth != ""){
                $date1 = date_create_from_format('Y-m-d', $dateofbirth);
                $year = $date1->format('Y');
                $curyear = date('Y');
    
                if (($curyear - $year) < 18) {
                    $requirement_error_messages.= "<div>You need at least 18 years old and above to register</div>";
                    $flag = 1;
                }
                }

            if (!empty($requirement_error_messages)) {
                echo "<div class='alert alert-danger'>";
                echo "<div>{$requirement_error_messages}</div>";
                echo "<div>Please update the correct requirement.</div>";
                echo "</div>";
                $flag == 1;
            }

            if ($image) {

                // upload to file to folder
                $target_directory = "uploads_cust/";
                $target_file = $target_directory . $image;
                $file_type = pathinfo($target_file, PATHINFO_EXTENSION);

                // error message is empty
                $file_upload_error_messages = "";

                // make sure that file is a real image
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if ($check !== false) {
                    // submitted file is an image
                } else {
                    $file_upload_error_messages .= "<div>Submitted file is not an image.</div>";
                    $flag = 1;
                }

                // make sure certain file types are allowed
                $allowed_file_types = array("jpg", "jpeg", "png", "gif");
                if (!in_array($file_type, $allowed_file_types)) {
                    $file_upload_error_messages .= "<div>Only JPG, JPEG, PNG, GIF files are allowed.</div>";
                    $flag = 1;
                }

                // make sure file does not exist
                if (file_exists($target_file)) {
                    $file_upload_error_messages .= "<div>Image already exists. Try to change file name.</div>";
                    $flag = 1;
                }

                // make sure submitted file is not too large, can't be larger than 1 MB
                if ($_FILES['image']['size'] > (1024000)) {
                    $file_upload_error_messages .= "<div>Image must be less than 1 MB in size.</div>";
                    $flag = 1;
                }

                // make sure the 'uploads' folder exists
                // if not, create it
                if (!is_dir($target_directory)) {
                    mkdir($target_directory, 0777, true);
                }

                // if $file_upload_error_messages is still empty
                if (empty($file_upload_error_messages)) {
                    // it means there are no errors, so try to upload the file
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        // it means photo was uploaded
                    } else {
                        echo "<div class='alert alert-danger'>";
                        echo "<div>Unable to upload photo.</div>";
                        echo "<div>Update the record to upload photo.</div>";
                        echo "</div>";
                        $flag = 1;
                    }
                }

                // if $file_upload_error_messages is NOT empty
                else {
                    // it means there are some errors, so show them to user
                    echo "<div class='alert alert-danger'>";
                    echo "<div>{$file_upload_error_messages}</div>";
                    echo "<div>Update the record to upload photo.</div>";
                    echo "</div>";
                    $flag = 1;
                }
            }else{
                $image = NULL;
            }

            if ($flag == 0){
                

                try {
                    // insert query
                    $query = "INSERT INTO customer SET username=:username, password=:password, firstname=:firstname, lastname=:lastname, gender=:gender, dateofbirth=:dateofbirth, account=:account, image=:image";
                    // prepare query for execution
                    $stmt = $con->prepare($query);
                    
                    // bind the parameters
                    $gender = $_POST['gender'];
                    $account = $_POST['account'];
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':password', $password);
                    $stmt->bindParam(':firstname', $firstname);
                    $stmt->bindParam(':lastname', $lastname);
                    $stmt->bindParam(':gender', $gender);
                    $stmt->bindParam(':dateofbirth', $dateofbirth);
                    $stmt->bindParam(':account', $account);
                    $stmt->bindParam(':image', $image);
                    // Execute the query
                    if ($stmt->execute()) {
                        header('Location: login.php?action=successful');
                        // now, if image is not empty, try to upload the image
                       
                    } else {
                        echo "<div class='alert alert-danger'>Unable to create account.</div>";
                    }
                }
                // show error
                catch (PDOException $exception) {
                    die('ERROR: ' . $exception->getMessage());
                }
            }
            
        }

        ?>



        <!-- html form here where the product information will be entered -->
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" enctype="multipart/form-data">
            <table class='table table-hover table-responsive table-bordered'>
                <tr>
                    <td>Username</td>
                    <td><input type='text' name='username' class='form-control' value="<?php if(isset($_POST['username'])){echo $_POST['username'];}?>"/></td>
                </tr>
                <tr>
                    <td>Photo</td>
                    <td><input type="file" name="image" /></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type="password" name='password' class='form-control' /></td>
                </tr>
                <tr>
                    <td>Confirm Password</td>
                    <td><input type="password" name='confpassword' class='form-control' /></td>
                </tr>
                <tr>
                    <td>First Name</td>
                    <td><input type='text' name='firstname' class='form-control' value="<?php if(isset($_POST['firstname'])){echo $_POST['firstname'];}?>"/></td>
                </tr>
                <tr>
                    <td>Last Name</td>
                    <td><input type='text' name='lastname' class='form-control' value="<?php if(isset($_POST['lastname'])){echo $_POST['lastname'];}?>"/></td>
                </tr>
                <tr>
                    <td>Gender</td>

                    <div>
                        <td><input type='radio' name='gender' value="male" />
                            <label for="male">Male</label><br>
                            <input type='radio' name='gender' value="female" />
                            <label for="female">Female</label>
                        </td>
                    </div>

                </tr>
                <tr>
                    <td>Date of birth</td>
                    <td><input type='date' name='dateofbirth' class='form-control' /></td>
                </tr>
                <!-- <tr>
                    <td>Registration date & time</td>
                    <td><input type='date' name='registration' class='form-control' /></td>
                </tr> -->
                <tr>
                    <td>Account status</td>
                    <div>
                        <td><input type='radio' name='account' value="active" checked />
                            <label for="active">active</label><br>
                            <input type='radio' name='account' value="inactive" />
                            <label for="inactive">inactive</label>
                        </td>
                    </div>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type='submit' value='Save' class='btn btn-primary' />
                        
                    </td>
                </tr>
            </table>
        </form>

    </div>
    <!-- end .container -->

    <!-- end .container -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>