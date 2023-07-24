<!DOCTYPE HTML>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update product</title>
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
            <h1>Update Product</h1>
        </div>
        <?php
        // get passed parameter value, in this case, the record ID
        // isset() is a PHP function used to verify if a value is there or not
        $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');



        //include database connection
        include 'config/database.php';




        // read current record's data
        try {
            // prepare select query
            $query = "SELECT id, name, description, price, promotion, manufacture, expire, image FROM products WHERE id = ? LIMIT 1";
            $stmt = $con->prepare($query);

            // this is the first question mark
            $stmt->bindParam(1, $id);

            // execute our query
            $stmt->execute();

            // store retrieved row to a variable
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // values to fill up our form
            $name = $row['name'];
            $description = $row['description'];
            $price = $row['price'];
            $promotion = $row['promotion'];
            $manufacture = $row['manufacture'];
            $expire_ = $row['expire'];
            $image = $row['image'];

        
            $store_image = "uploads_prod/";
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
        // check if form was submitted
        if ($_POST) {

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

            if ($name == "" || $description == ""  ||  $price == ""  || $manufacture == "") {
                $requirement_error_messages .= "<div>You need to futfil all the requirement</div>";
                $flag = 1;
            }

            if ($promotion != "") {
                if (is_numeric($price) || is_numeric($promotion)) {
                    if ($promotion > $price) {
                        $requirement_error_messages .=  "<div>Promotion price should not be more than original price</div>";
                        $flag = 1;
                    }
                } else {
                    $requirement_error_messages .=  "<div>You should input correct number for price and promotion</div>";
                    $flag = 1;
                }
            } else {
                $promotion = NULL;
            }


            if ($expire_ != "") {
                $date1 = date_create($manufacture);
                $date2 = date_create($expire_);
                $diff = date_diff($date1, $date2);
                if (($diff->format("%R%a days")) < 0) {
                    $requirement_error_messages .= "<div>The expire time should be later than manufacture time</div>";
                    $flag = 1;
                }
            } else {
                $expire_ = NULL;
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
                $target_directory = "uploads_prod/";
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
            } else {
                $image = NULL;
            }

            if ($flag == 0) {
                try {
                    // write update query
                    // in this case, it seemed like we have so many fields to pass and
                    // it is better to label them and not use question marks
                    $query = "UPDATE products
                  SET name=:name, description=:description,
                 price=:price, promotion=:promotion, manufacture=:manufacture,
                  expire=:expire, image=:image WHERE id = :id";
                    // prepare query for excecution
                    $stmt = $con->prepare($query);

                    $flag_same_image = false;

                    if ($image != "NULL" && empty($_POST['images_remove'])) {
                        $image = pathinfo($old_image, PATHINFO_BASENAME);
                        $flag_same_image = true;
                        // checked and not upload new image
                    } else if ($image == "NULL" && !empty($_POST['images_remove'])) {
                        $image = "NULL";
                    }

                    // posted values
                    $name = htmlspecialchars(strip_tags($_POST['name']));
                    $description = htmlspecialchars(strip_tags($_POST['description']));
                    $price = htmlspecialchars(strip_tags($_POST['price']));
                    $promotion = htmlspecialchars(strip_tags($_POST['promotion']));
                    $manufacture = htmlspecialchars(strip_tags($_POST['manufacture']));
                    $expire = htmlspecialchars(strip_tags($_POST['expire']));
                    // bind the parameters
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':price', $price);
                    $stmt->bindParam(':promotion', $promotion);
                    $stmt->bindParam(':manufacture', $manufacture);
                    $stmt->bindParam(':expire', $expire);
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':image', $image);
                    // Execute the query
                    if ($stmt->execute()) {
                        // if the image not same then remove previous one and not the default one
                        if (!$flag_same_image && !strpos($old_image, "no_image.jpg")) {
                            unlink($old_image);
                        }

                        echo "<script type=\"text/javascript\"> window.location.href='product_read.php?action=successful'</script>";
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
                    <td>
                        <?php


                        $query = "SELECT image FROM products ORDER BY id DESC";
                        $stmt = $con->prepare($query);
                        $stmt->execute();
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($image != "") {
                            echo " <div class='text-center'> <img src='uploads_prod/$image' height='200' width='200' alt='none' /><br></div>";
                            echo " <div class='text-center mt-2' > <input type='checkbox' id='images_remove' name='images_remove' value='Yes />
                            <label for='images_remove'>Remove Image</label></div>";
                        } else {
                            echo " <input type='file' name='image' /></td>";
                        }

                        ?>

                </tr>
                <tr>
                    <td>Description</td>
                    <td><textarea name='description' class='form-control'><?php echo htmlspecialchars($description, ENT_QUOTES);  ?></textarea></td>
                </tr>
                <tr>
                    <td>Price</td>
                    <td><input type='text' name='price' value="<?php echo htmlspecialchars($price, ENT_QUOTES);  ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Promotion Price</td>
                    <td><input type='text' name='promotion' value="<?php echo htmlspecialchars($promotion, ENT_QUOTES);  ?>" class='form-control' /></td>
                </tr>
                <tr>
                    <td>Manufacture Date</td>
                    <td><input type='date' name='manufacture' value="<?php echo date('Y-m-d', strtotime($manufacture, ENT_QUOTES));  ?>" class='form-control' /></td>


                </tr>
                <tr>
                    <td>Expired Date</td>
                    <td><input type='date' name='expire' class='form-control' value=<?php
                     if ($expire_ != NULL) {
                    echo date('Y-m-d', strtotime($expire_, ENT_QUOTES));
                     }
                     ?> />
                    </td>

                    <!-- <td><input type='date' name='expire' value="<?php echo $row['expire'];  ?>" class='form-control' /></td> -->

                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type='submit' value='Save Changes' class='btn btn-primary' />
                        <a href='product_read.php' class='btn btn-danger'>Back to read products</a>
                    </td>
                </tr>
            </table>
        </form>

    </div>
    <!-- confirm delete record will be here -->

    <!-- end .container -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>