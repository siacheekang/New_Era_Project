<!DOCTYPE HTML>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer update</title>
    <!-- Latest compiled and minified Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous"> <!-- custom css -->

</head>

<body style="background-color: #FFF8EA">


    <?php
    include 'config/navbar.php';
    ?>
    <!-- container -->
    <div class="container">
        <div class="page-header">
            <h1>Update Customer</h1>
        </div>
        <!-- PHP read record by ID will be here -->
        <?php
        // get passed parameter value, in this case, the record ID
        // isset() is a PHP function used to verify if a value is there or not
        $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');

        //include database connection
        include 'config/database.php';



        // read current record's data
        try {
            // prepare select query
            $query = "SELECT * FROM customer WHERE id = ? LIMIT 1";
            $stmt = $con->prepare($query);

            // this is the first question mark
            $stmt->bindParam(1, $id);

            // execute our query
            $stmt->execute();

            // store retrieved row to a variable
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // values to fill up our form
            $name = $row['username'];
            $datebirth = $row['dateofbirth'];
            $account = $row['account'];
            $password = $row['password'];
            $firstname = $row['firstname'];
            $lastname = $row['lastname'];
            $gender = $row['gender'];
            $registration = $row['registration'];
            $image = $row['image'];

            $store_image = "uploads_cust/";
            if (!empty($row['image']) && $row['image'] != "NULL") {
                $old_image = $store_image . $row['image'];
            } else {
                $old_image = $store_image . "no_image.jpg";
            }
        }

        // show error
        catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }
        ?>

        <!-- HTML form to update record will be here -->
        <!-- PHP post to update record will be here -->
        <?php
        // flag if form was submitted
        if ($_POST) {

            $confirmpassword = $_POST['confpassword'];
            $newpassword = $_POST['newpassword'];

            // new 'image' field
            $image = !empty($_FILES["image"]["name"])
                ? sha1_file($_FILES['image']['tmp_name']) . "-" . basename($_FILES["image"]["name"])
                : NULL;
            $image = htmlspecialchars(strip_tags($image));

            if (empty($image) && $image == "NULL") {
                $image = $store_image . "no_image.jpg";
            }

            $flag = 0;

            $requirement_error_messages = "";

            if($username != ""){
                try{
            $query_name = "SELECT username FROM customer WHERE username='$username' ";
            $stmt_name = $con->prepare($query_name);
            $stmt_name->execute();
    
            // this is how to get number of rows returned
            $num_name = $stmt_name->rowCount();
    
    
            //check if more than 0 record found
            if ($num_name > 0) {
                $requirement_error_messages.="<div>The username has been created by another user</div>";
                    $flag =1;
            }
        } catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }
        }
        
            if (strlen($_POST['name']) > 6) {
                if (strpos(trim($_POST['name']), " ")) {
                    $requirement_error_messages .= "<div>The username should not have blank</div>";
                    $flag = 1;
                } 
            } else {
                $requirement_error_messages .= "<div>The username should include at least 6 character</div>";
                $flag = 1;
            }

            if (!empty($_POST['password']) || !empty($_POST['newpassword']) || !empty($_POST['confpassword'])) {
                if (empty($_POST['password'])) {
                    $requirement_error_messages .= "<div>If want to change new passward then old password field can not be empty!</div>";
                    $flag = 1;
                }

                if (empty($_POST['newpassword'])) {
                    $requirement_error_messages .= "<div>If want to change new passward then new password field can not be empty!</div>";
                    $flag = 1;
                }

                if (empty($_POST['confpassword'])) {
                    $requirement_error_messages .= "<div>If want to change new passward then confirm password field can not be empty!</div>";
                    $flag = 1;
                }
               

                    if ($_POST['password'] != $password) {
                        $requirement_error_messages .= "<div>The password not exists</div>";
                        $flag = 1;
                    }

                    if (strlen($_POST['newpassword']) > 8) {
                        if (preg_match('/[A-Z]/', $_POST['newpassword'])) {
                            if (preg_match('/[a-z]/', $_POST['newpassword'])) {
                                if (preg_match('/[0-9]/',  $_POST['newpassword'])) {
                                    echo "<div class='alert alert-success'>Strong Password.</div>";
                                } else {
                                    $requirement_error_messages .= "<div>The password should include number</div>";
                                    $flag = 1;
                                }
                            } else {
                                $requirement_error_messages .= "<div>The password should include small letter</div>";
                                $flag = 1;
                            }
                        } else {
                            $requirement_error_messages .= "<div>The password should include capital letter</div>";
                            $flag = 1;
                        }
                    } else {

                        $requirement_error_messages .= "<div>The password should  include at least 8 character</div>";
                        $flag = 1;
                    }



                    if ($newpassword != $confirmpassword) {
                        $requirement_error_messages .= "<div>Your password is not equall</div>";
                        $flag = 1;
                    }
                
            }



            $date1 = date_create_from_format('Y-m-d', $_POST['dateofbirth']);
            $year = $date1->format('Y');
            $curyear = date('Y');

            if (($curyear - $year) < 18) {
                $requirement_error_messages .= "<div>You need at least 18 years old and above to register</div>";
                $flag = 1;
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
                    $flag == 1;
                }

                // make sure certain file types are allowed
                $allowed_file_types = array("jpg", "jpeg", "png", "gif");
                if (!in_array($file_type, $allowed_file_types)) {
                    $file_upload_error_messages .= "<div>Only JPG, JPEG, PNG, GIF files are allowed.</div>";
                    $flag == 1;
                }

                // make sure file does not exist
                if (file_exists($target_file)) {
                    $file_upload_error_messages .= "<div>Image already exists. Try to change file name.</div>";
                    $flag == 1;
                }

                // make sure submitted file is not too large, can't be larger than 1 MB
                if ($_FILES['image']['size'] > (1024000)) {
                    $file_upload_error_messages .= "<div>Image must be less than 1 MB in size.</div>";
                    $flag == 1;
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
                        $flag == 1;
                    }
                }

                // if $file_upload_error_messages is NOT empty
                else {
                    // it means there are some errors, so show them to user
                    echo "<div class='alert alert-danger'>";
                    echo "<div>{$file_upload_error_messages}</div>";
                    echo "<div>Update the record to upload photo.</div>";
                    echo "</div>";
                    $flag == 1;
                }
            } else {
                $image = NULL;
            }


            if ($flag == 0) {
                try {
                    // write update query
                    // in this case, it seemed like we have so many fields to pass and
                    // it is better to label them and not use question marks
                    $query = "UPDATE customer
                  SET username=:username, dateofbirth=:dateofbirth,
                    account=:account , password=:password, firstname=:firstname, 
                    lastname=:lastname, gender=:gender, image=:image WHERE id = :id";
                    // prepare query for excecution
                    $stmt = $con->prepare($query);

                    $flag_same_image = false;

                    if ($image != "NULL" && empty($_POST['images_remove'])) {
                        $image = pathinfo($old_image, PATHINFO_BASENAME);
                        $flag_same_image = true;
                        // flaged and not upload new image
                    } else if ($image == "NULL" && !empty($_POST['images_remove'])) {
                        $image = "NULL";
                    }

                    // posted values
                    $name = htmlspecialchars(strip_tags($_POST['name']));
                    $datebirth = htmlspecialchars(strip_tags($_POST['dateofbirth']));
                    $account = htmlspecialchars(strip_tags($_POST['account']));
                    ///write one since upside stop for password and other not need
                    if (!empty($_POST['password'])) {
                        $password = htmlspecialchars(strip_tags($_POST['password']));
                    }
                    $firstname = htmlspecialchars(strip_tags($_POST['firstname']));
                    $lastname = htmlspecialchars(strip_tags($_POST['lastname']));
                    $gender = htmlspecialchars(strip_tags($_POST['gender']));
                    
                    // bind the parameters
                    $stmt->bindParam(':username', $name);
                    $stmt->bindParam(':dateofbirth', $datebirth);
                    $stmt->bindParam(':account', $account);
                    $stmt->bindParam(':password', $password);
                    $stmt->bindParam(':firstname', $firstname);
                    $stmt->bindParam(':lastname', $lastname);
                    $stmt->bindParam(':gender', $gender);
                    
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':image', $image);
                    // Execute the query
                    if ($stmt->execute()) {
                        // if the image not same then remove previous one and not the default one
                        if (!$flag_same_image && !strpos($old_image, "no_image.jpg")) {
                            unlink($old_image);
                        }

                        echo "<script type=\"text/javascript\"> window.location.href='customer_read.php?action=successful'</script>";
                    } else {
                        echo "<div class='alert alert-danger'>Unable to update record. Please try again.</div>";
                    }
                }
                // show errors
                catch (PDOException $exception) {
                    die('ERROR: ' . $exception->getMessage());
                }
            }
        } ?>




        <!--we have our html form here where new record information can be updated-->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id={$id}"); ?>" method="post" enctype="multipart/form-data">
            <table class='table table-hover table-responsive table-bordered'>
                <tr>
                    <td>Name</td>
                    <td><input type='text' name='name' value="<?php echo htmlspecialchars($name, ENT_QUOTES);  ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Photo</td>
                    <td><?php


                        $query = "SELECT image FROM customer ORDER BY id DESC";
                        $stmt = $con->prepare($query);
                        $stmt->execute();
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($image != "") {
                            echo " <div class='text-center'> <img src='uploads_cust/$image' height='200' width='200' alt='none' /><br></div>";
                            echo "  <div class='text-center mt-2'> <input type='checkbox' id='images_remove' name='images_remove' value='Yes />
                            <label for='images_remove'>Remove Image</label></div>";
                        } else {
                            echo " <input type='file' name='image' /></td>";
                        }

                        ?>
                        
                </tr>
                <tr>
                    <td>Old Password</td>
                    <td><input type='password' name='password' class='form-control' placeholder="leave blank if not need to change password" /></td>
                </tr>
                <tr>
                    <td>New Password</td>
                    <td><input type="password" name='newpassword' class='form-control' placeholder="leave blank if not need to change password"/></td>
                </tr>
                <tr>
                    <td>Confirm Password</td>
                    <td><input type="password" name='confpassword' class='form-control' placeholder="leave blank if not need to change password"/></td>
                </tr>
                <tr>
                    <td>First Name</td>
                    <td><input type='text' name='firstname' value="<?php echo htmlspecialchars($firstname, ENT_QUOTES);  ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Last Name</td>
                    <td><input type='text' name='lastname' value="<?php echo htmlspecialchars($lastname, ENT_QUOTES);  ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Gender</td>

                    <div>
                        <td>
                            <input type='radio' name='gender' value="male" <?php if($gender == "male") {
                                                                                echo "checked";
                                                                            } ?> />
                            <label for="male">Male</label><br>
                            <input type='radio' name='gender' value="female" <?php if ($gender == "female") {
                                                                                    echo "checked";
                                                                                } ?> />
                            <label for="female">Female</label>
                        </td>
                    </div>

                </tr>
                <tr>
                    <td>Date of birth</td>
                    <td><input type='date' name='dateofbirth' value="<?php echo date('Y-m-d', strtotime($datebirth));  ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Registration date</td>
                    <!-- <td><input type='date' name='registration' value="<?php echo date('Y-m-d', strtotime($registration));  ?>" class='form-control' /></td> -->
                    <td name='registration'><?php echo date('d/m/Y',strtotime($registration));  ?></td>
                </tr>
                <tr>
                    <td>Account status</td>
                   
                    <div>
                        <td>
                            <input type='radio' name='account' value="active" <?php if($account == "active") {
                                                                                echo "checked";
                                                                            } ?> />
                            <label for="active">active</label><br>
                            <input type='radio' name='account' value="inactive" <?php if ($account == "inactive") {
                                                                                    echo "checked";
                                                                                } ?> />
                            <label for="inactive">inactive</label>
                        </td>
                    </div>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        <input type='submit' value='Save Changes' class='btn btn-primary' />
                        <a href='customer_read.php' class='btn btn-danger'>Back to read customer</a>
                    </td>
                </tr>
            </table>
        </form>

    </div>
    <!-- end .container -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>