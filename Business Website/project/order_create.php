<?php
session_start();
include 'config/usersign.php';
?>

<!DOCTYPE HTML>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Order</title>
    <!-- Latest compiled and minified Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body style="background-color: #FFF8EA">


    <?php
    include 'config/navbar.php';
    ?>

    <div class="container">
        <div class="page-header my-3">
            <h1>Order Form</h1>
        </div>



        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
            <div class="input-group mb-3">
                <label class="input-group-text" for="inputGroupSelect00">Username</label>
                <select class="form-select" id="inputGroupSelect00" name="customer_id">
                    <option selected value=''>Please select your username</option>
                    <?php
                    // include database connection
                    include 'config/database.php';

                    // delete message prompt will be here

                    // select all data
                    $query = "SELECT id,username FROM customer ORDER BY id DESC";
                    $stmt = $con->prepare($query);
                    $stmt->execute();

                    // this is how to get number of rows returned
                    $num = $stmt->rowCount();


                    //check if more than 0 record found
                    if ($num > 0) {

                        // retrieve our table contents
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            // extract row
                            // this will make $row['firstname'] to just $firstname only
                            extract($row);
                            // creating new table row per record

                            echo "<option value=" . $id . ">" . $username . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <table id="order">
                <?php
                include 'config/database.php';
                $query = "SELECT id, name, price FROM products ORDER BY id DESC";
                $stmt = $con->prepare($query);
                $stmt->execute();

                // this is how to get number of rows returned
                $num = $stmt->rowCount();

                echo "<tr class='pRow'>";
                echo "<td></td>";
                echo "<td>";
                echo "<div class='input-group '>";
                echo "<label class='input-group-text' for='inputGroupSelect'>Product</label>";
                echo "<select name='product_id[]' class='form-select'>";
                echo " <option  selected value='-1'>Please select item</option>";
          

                //check if more than 0 record found
                if ($num > 0) {

                    // retrieve our table contents
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        // extract row
                        // this will make $row['firstname'] to just $firstname only
                        extract($row);
                        // creating new table row per record

                        echo "<option value=" . $id .">" . $name .  "</option>";
                    }
                }

                echo "</select>";
                echo "</div>";
                echo "</td>";
                echo "<td>";
                echo "<input type='number' value='' name='quantity[]' class='form-select'>";
                echo "</td>";
                echo "</tr>";
                

                ?>

            </table>
            <div class="container d-flex p-0 my-3">
                <input type="button" value="Add More Product" class="add_column btn btn-primary mx-2" />
                <input type="button" value="Delete" class="delete_column btn btn-danger mx-2" />
            </div>
            <div class=" my-4 text-center">
                <input type='submit' value='Done' class='btn btn-primary' />

            </div>
        </form>






        <?php

        if ($_POST) {
            include 'config/database.php';
            $customer_id = $_POST['customer_id'] != '' ? $_POST['customer_id'] : NULL;
            $quantity = $_POST['quantity'] != '' ? $_POST['quantity'] : NULL;
            $product_id = $_POST['product_id'] != '-1' ? $_POST['product_id'] : NULL;

            $flag = 0;
            if ($customer_id == '') {
                echo "<div class='alert alert-danger'>Please make sure all fields are not emplty!</div>";
                $flag = 1;
            }
            for ($check = 0; $check < count($_POST['product_id']); $check++) {
                if ($_POST['product_id'][$check] == -1) {
                    echo "<div class='alert alert-danger'>Please make sure your product are not emplty!</div>";
                    $flag = 1;
                    break;
                }
                if ($_POST["quantity"][$check] == 0) {
                    echo "<div class='alert alert-danger'>Please make sure your quantity are not emplty!</div>";
                    $flag = 1;
                    break;
                }
            }

            if ($flag == 0){
               
                $order_id = 0;

                $query = "INSERT INTO order_summary SET customer_id=:customer_id, date=:order_date";
                $stmt = $con->prepare($query);
                $stmt->bindParam(':customer_id', $customer_id);
                $order_date = date('Y-m-d');
                $stmt->bindParam(':order_date', $order_date);


                if ($stmt->execute()) {
                    $query_order_summary = "SELECT MAX(order_id) from order_summary";
                    $stmt_order_summary = $con->prepare($query_order_summary);
                    $stmt_order_summary->execute();
                    $num = $stmt_order_summary->rowCount();

                    if ($num > 0) {
                        $row = $stmt_order_summary->fetch(PDO::FETCH_ASSOC);
                        $order_id = $row['MAX(order_id)'];
                    }

                    try {

                        for ($loop = 0; $loop < count($_POST['product_id']); $loop++) {
                            $product_id = $_POST['product_id'][$loop];
                            $quantity = $_POST['quantity'][$loop];

                            // insert query
                            $query_order_detail = "INSERT INTO order_detail SET order_id=:order_id, product_id=:product_id, quantity=:quantity";
                            // prepare query for execution
                            $stmt_order_detail = $con->prepare($query_order_detail);
                            // bind the parameters
                            $stmt_order_detail->bindParam(':product_id', $product_id);
                            $stmt_order_detail->bindParam(':quantity', $quantity);
                            $stmt_order_detail->bindParam(':order_id', $order_id);
                            // Execute the query
                            if ($stmt_order_detail->execute()) {
                                $flag = 0;
                            } else {
                                echo "<div class='alert alert-danger'>Unable to save product.</div>";
                            }
                        }
                        if ($flag == 0) {
                            echo "<div class='alert alert-success'>Product was saved.</div>";
                        }
                    }

                    // show error
                    catch (PDOException $exception) {
                        die('ERROR: ' . $exception->getMessage());
                    }
                } else {
                    echo "<div class='alert alert-danger'>Unable to save record by total price.</div>";
                }
            }
        }
        ?>







    </div>

    <script>
        document.addEventListener('click', function(event) {
            if (event.target.matches('.add_column')) {
                var element = document.querySelector('.pRow');
                var clone = element.cloneNode(true);
                element.after(clone);
            }
            if (event.target.matches('.delete_column')) {
                var total = document.querySelectorAll('.pRow').length;
                if (total > 1) {
                    var element = document.querySelector('.pRow');
                    element.remove(element);
                }
            }
            var total = document.querySelectorAll('.pRow').length;

            var row = document.getElementById('order').rows;
            for (var i =1; i <= total; i++) {
                cells[0].innerHTML = i;

            }
        }, false);
    </script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>