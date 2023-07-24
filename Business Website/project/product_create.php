<?php
session_start();
include 'config/usersign.php';
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PDO - Create a Record - PHP CRUD Tutorial</title>
    <!-- Latest compiled and minified Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body style="background-color: #FFF8EA">


    <?php
    include 'config/navbar.php';
    include 'config/database.php';
    ?>

    <!-- container -->
    <div class="container">
        <div class="page-header my-3">
            <h1>Create Product</h1>
        </div>

        <?php
        if ($_POST) {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $manufacture = $_POST['manufacture'];
            $promotion = $_POST['promotion'];
            $expire = $_POST['expire'];

            $flag = 0;

            // new 'image' field
            $image = !empty($_FILES["image"]["name"])
                ? sha1_file($_FILES['image']['tmp_name']) . "-" . basename($_FILES["image"]["name"])
                : NULL;
            $image = htmlspecialchars(strip_tags($image));

            $requirement_error_messages = "";

            if ($name == "" || $description == ""  ||  $price == ""  || $manufacture == "") {
                $requirement_error_messages .=  "<div>You need to futfil all the requirement.</div>";
                $flag = 1;
            }

            if ($promotion != "") {
                if (is_numeric($price) && is_numeric($promotion)) {
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

            if ($expire != "") {
                $date1 = date_create($manufacture);
                $date2 = date_create($expire);
                $diff = date_diff($date1, $date2);
                if (($diff->format("%R%a days")) < 0) {
                    $requirement_error_messages .= "<div>The expire time should be later than manufacture time</div>";
                    $flag = 1;
                }
            } else {
                $expire = NULL;
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
                if ($check === false) {

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
            }


            if ($flag == 0) {


                try {
                    // insert query
                    $query = "INSERT INTO products SET name=:name, description=:description, price=:price, created=:created, promotion=:promotion, manufacture=:manufacture, expire=:expire, image=:image";
                    // prepare query for execution
                    $stmt = $con->prepare($query);


                    // bind the parameters
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':price', $price);
                    $stmt->bindParam(':promotion', $promotion);
                    $stmt->bindParam(':manufacture', $manufacture);
                    $stmt->bindParam(':expire', $expire);
                    $created = date('Y-m-d H:i:s'); // get the current date and time
                    $stmt->bindParam(':created', $created);
                    $stmt->bindParam(':image', $image);
                    // Execute the query
                    if ($stmt->execute()) {
                        header('Location: product_read.php?action=successful');
                    } else {
                        echo "<div class='alert alert-danger'>Unable to save record.</div>";
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
                    <td>Name</td>
                    <td><input type='text' name='name' class='form-control' /></td>
                </tr>
                <tr>
                    <td>Photo</td>
                    <td><input type="file" name="image" /></td>
                </tr>

                <tr>
                    <td>Description</td>
                    <td><textarea name="description" cols="50" rows="8"></textarea></td>
                </tr>
                <tr>
                    <td>Price</td>
                    <td><input type='text' name='price' class='form-control' /></td>
                </tr>
                <tr>
                    <td>Promotion Price</td>
                    <td><input type='text' name='promotion' class='form-control' /></td>
                </tr>
                <tr>
                    <td>Manufacture Date</td>
                    <td><input type='date' name='manufacture' class='form-control' /></td>
                </tr>
                <tr>
                    <td>Expired Date</td>
                    <td><input type='date' name='expire' class='form-control' /></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type='submit' value='Save' class='btn btn-primary' />
                        <a href='product_read.php' class='btn btn-danger'>Back to read products</a>
                    </td>
                </tr>
            </table>
        </form>

    </div>
    <!-- end .container -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>




</html>