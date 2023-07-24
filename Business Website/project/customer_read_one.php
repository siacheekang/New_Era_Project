<?php
session_start();
include 'config/usersign.php';
?>

<!DOCTYPE HTML>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer detail info</title>
    <!-- Latest compiled and minified Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body style="background-color: #FFF8EA">

    <?php
    include 'config/navbar.php';
    ?>

    <!-- container -->
    <div class="container">
        <div class="page-header">
            <h1>Read Customer</h1>
        </div>

        <!-- PHP read one record will be here -->
        <?php
        // get passed parameter value, in this case, the record ID
        // isset() is a PHP function used to verify if a value is there or not
        $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');

        //include database connection
        include 'config/database.php';

        // read current record's data
        try {
            // prepare select query
            $query = "SELECT id, username, gender, firstname, lastname, dateofbirth, registration, account, image FROM customer WHERE id = :id ";
            $stmt = $con->prepare($query);

            // Bind the parameter
            $stmt->bindParam(":id", $id);

            // execute our query
            $stmt->execute();

            // store retrieved row to a variable
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // values to fill up our form
            $username = $row['username'];
            $image = $row['image'];
            $gender = $row['gender'];
            $firstname = $row['firstname'];
            $lastname = $row['lastname'];
            $dateofbirth = $row['dateofbirth'];
            $registration = $row['registration'];
            $account = $row['account'];
            // shorter way to do that is extract($row)
        }

        // show error
        catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }
        ?>


        <!-- HTML read one record table will be here -->
        <!--we have our html table here where the record will be displayed-->
        <table class='table table-hover table-responsive table-bordered'>
            <tr>
                <td>Name</td>
                <td><?php echo htmlspecialchars($username, ENT_QUOTES);  ?></td>
            </tr>
            <tr>
                <td>Photo</td>
                <td>
                <?php  
                
                
                $query = "SELECT image FROM customer ORDER BY id DESC" ;  
                $stmt = $con->prepare($query); 
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                                    
            
                               
                if($image == NULL || empty($image)){
                    echo "  <img src='uploads_prod/no_image.jpg' height='200' width='200' alt='none' />" ;
                }else{
                    echo "  <img src='uploads_cust/$image' height='200' width='200' alt='none' />" ;
                }
                  
                ?>  
                </td>
                
            </tr>
          
            <tr>
                <td>Gender</td>
                <td><?php echo htmlspecialchars($gender, ENT_QUOTES);  ?></td>
            </tr>
            <tr>
                <td>First Name</td>
                <td><?php echo htmlspecialchars($firstname, ENT_QUOTES);  ?></td>
            </tr>
            <tr>
                <td>Last Name</td>
                <td><?php echo htmlspecialchars($lastname, ENT_QUOTES);  ?></td>
            </tr>
            <tr>
                <td>Date of Birth</td>
                <td><?php echo htmlspecialchars($dateofbirth, ENT_QUOTES);  ?></td>
            </tr>
            <tr>
                <td>Registration</td>
                <td><?php echo htmlspecialchars($registration, ENT_QUOTES);  ?></td>
            </tr>
            <tr>
                <td>Account status</td>
                <td><?php echo htmlspecialchars($account, ENT_QUOTES);  ?></td>
            </tr>
            <tr>
                <td></td>
                <td>
                <a href='<?php echo "customer_update.php?id={$id}"?>' class='btn btn-danger'>Edit customer</a>
                    <a href='customer_read.php' class='btn btn-danger'>Back to read customer</a>
                </td>
            </tr>
        </table>

    </div> <!-- end .container -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>