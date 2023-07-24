<?php
session_start();
include 'config/usersign.php';
?>

<!DOCTYPE HTML>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Order details</title>
    <!-- Latest compiled and minified Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body style="background-color: #FFF8EA">


    <?php
    include 'config/navbar.php';

    ?>

    <div class="container ">
        <div class="page-header my-3">
            <h1>Update Order</h1>
            <?php
            $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : die('ERROR: Record ID not found.');

            include 'config/database.php';

            $query_getdb = "SELECT order_summary.order_id, order_detail.order_detail_id, order_detail.product_id, order_detail.quantity, customer.username FROM order_summary INNER JOIN order_detail ON order_summary.order_id = order_detail.order_id INNER JOIN customer ON order_summary.customer_id=customer.id WHERE order_summary.order_id=:order_id";
            $stmt_getdb = $con->prepare($query_getdb);
            $stmt_getdb->bindParam('order_id', $order_id);
            $stmt_getdb->execute();
            $row_ = $stmt_getdb->fetch(PDO::FETCH_ASSOC);
            $username = $row_['username'];

            $num_getdb = $stmt_getdb->rowCount();



            ?>
        </div>

        <form action="<?php echo $_SERVER["PHP_SELF"] . "?order_id={$order_id}"; ?>" method="POST" id="thisform">
            <div class="container d-flex p-0 my-3">
                <div class="form-floating font-dark w-50">

                    <select class="form-select" name='customer_id'>
                        <?php


                        $query20 = "SELECT id, username FROM customer";
                        $stmt20 = $con->prepare($query20);
                        $stmt20->execute();
                        $num = $stmt20->rowCount();
                        if ($num > 0) {

                            while ($row = $stmt20->fetch(PDO::FETCH_ASSOC)) {
                                $username_id = $row['username'];
                                $is_selected = $username == $username_id ? 'selected' : '';
                                echo "<option " . $is_selected . " value=" . $row['id'] . ">" . $username_id . "</option>";
                            }
                        }


                        ?>
                    </select>
                    <label class="text-dark" for="floatingSelect">Username</label>
                </div>
            </div>
            <div>
                <div>Previous Order</div>
                <table class='table table-responsive table-bordered' id="order">
                    <tr>
                        <th>Number</th>
                        <th>Product</th>
                        <th>Quantity</th>
                    </tr>
                    <?php
                    $query_readproduct = "SELECT id, name FROM products ORDER BY id DESC";
                    $stmt_readproduct = $con->prepare($query_readproduct);
                    $stmt_readproduct->execute();
                    $num_readproduct = $stmt_readproduct->rowCount();

                    //read product first from db
                    if ($num_getdb > 0) {

                        $index=0;
                        do {
                            $stmt_readproduct->execute();
                            echo "<tr class='pRow'>";
                            echo "<td>";
                            echo $index+1;
                            echo "</td>";
                            echo "<td>";
                            echo "<select class='form-select' name='product_id[]'>";
                            if ($num_readproduct > 0) {
                                while ($row_readproduct = $stmt_readproduct->fetch(PDO::FETCH_ASSOC)) {
                                    extract($row_readproduct);
                                    echo "<option value='{$id}'";
                                    if ($row_['product_id'] == $row_readproduct['id']) {
                                        echo "selected";
                                    }
                                    echo ">{$name}</option>";
                                }
                            } else {
                                echo "<option> No Products Founded</option>";
                            }
                            echo "</select>";
                            echo "</td>";
                            echo "<td>";
                            echo "<input type='number' name='quantity[]' value='" . $row_['quantity'] . "' class='form-select'>";
                            echo "</td>";
                            echo "<td>";
                            echo "<div onclick=\"drop_item()\" class=\"btn btn-danger drop_item\">Delete</div>";
                            echo "</td>";
                            echo "</tr>";
                            $index++;
                        } while ($row_ = $stmt_getdb->fetch(PDO::FETCH_ASSOC));
                    }

                    
                    ?>

                </table>
                <div>New Update</div>
                <table  class='table table-responsive table-bordered' id="order1">
                    <?php
                    for ($p_c = 1; $p_c <= 1; $p_c++) {
                        $stmt_readproduct->execute();
                        echo "<tr class='empty_pRow'>";
                        echo "<td>Added</td>";
                        echo "<td>";
                        echo "<select class='form-select' name='product_id[]'>";
                        echo "<option value='NULL' selected>--</option>";
                        if ($num_readproduct > 0) {
                            while ($row_readproduct = $stmt_readproduct->fetch(PDO::FETCH_ASSOC)) {
                                extract($row_readproduct);
                                echo "<option value='{$id}'>{$name}</option>";
                            }
                        } else {
                            echo "<option> No Products Founded</option>";
                        }
                        echo "</select>";
                        echo "</td>";
                        echo "<td>";
                        echo "<input type='number' name='quantity[]' class='form-select'>";
                        echo "</td>";
                        echo "<td>";
                        echo "<div onclick='drop_item()' class=\"btn btn-danger drop_item\">Delete</div>";
                        echo "</td>";
                        echo "</tr>";
                    }
                ?></table>
                    
                
            </div>
            <div class="container d-flex p-0 my-3">
                <input type="button" value="Add More Product"  class="add_column btn btn-primary mx-2" />
               

                <input type='button' value='Done' onclick="checkDuplicate()" class='btn btn-secondary mx-2' />
            </div>
        </form>

    </div>

    <?php

    if ($_POST) {


        $customer_id = $_POST['customer_id'];
        if (empty($customer_id)) {
            echo "<div class='alert alert-danger'>Please make sure all fields are not emplty!</div>";
        } else {

            $delete_ord_details = "DELETE FROM order_detail WHERE order_id=:order_id";

            $stmt_ord_details = $con->prepare($delete_ord_details);
            $stmt_ord_details->bindParam(':order_id', $order_id);
            $stmt_ord_details->execute();

            $query = "UPDATE order_summary SET customer_id=:customer_id, date=:order_date WHERE order_id=:order_id";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':customer_id', $customer_id);
            $order_date = date('Y-m-d');
            $stmt->bindParam(':order_date', $order_date);
            $stmt->bindParam(':order_id', $order_id);
            if ($stmt->execute()) {

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
                        echo "<script type=\"text/javascript\"> window.location.href='order_detail.php?order_id={$order_id}'</script>";
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


    <script>
       document.addEventListener('click', function(event) {
            if (event.target.matches('.add_column')) {
                var table = document.querySelectorAll('.empty_pRow');
                var rowCount = table.length;
                var clone = table[rowCount - 1].cloneNode(true);
                table[rowCount - 1].after(clone);
            }

          
            var total = document.querySelectorAll('.empty_pRow').length;

            var row = document.getElementById('order').rows;
            for (var i = 1; i <= total; i++) {
                row[i].cells[0].innerHTML = i;

            }
        }, false);

        function checkDuplicate() {
            var newarray = [];
            var selects = document.getElementsByTagName('select');
            for (var i = 0; i < selects.length; i++) {
                newarray.push(selects[i].value);
            }
            if (newarray.length !== new Set(newarray).size) {
                alert("There are Duplicate item!");
            } else {
                document.getElementById('thisform').submit();
            }
        }

        function drop_item() {
            document.querySelector('#order').onclick = function(ev) {
                // only if the innerHTML (tag content) is "Delete"
                if (ev.target.innerHTML == "Delete") {
                    // get all the tag which name as ".pRow"
                    var table = document.querySelectorAll('.pRow');
                    var rowCount = table.length;

                    // if the table row is lager than 1
                    if (rowCount > 1) {
                        // get the row tag (tr)
                        var table_row = ev.target.parentElement.parentElement;
                        table_row.remove(table_row);
                    } else {
                        alert("You must remained at least one row in the table");
                    }

                }
            }

            document.querySelector('#order1').onclick = function(ev) {
                // only if the innerHTML (tag content) is "Delete"
                if (ev.target.innerHTML == "Delete") {
                    // get all the tag which name as ".pRow"
                    var table = document.querySelectorAll('.empty_pRow');
                    var rowCount = table.length;

                    // if the table row is lager than 1
                    if (rowCount > 1) {
                        // get the row tag (tr)
                        var table_row = ev.target.parentElement.parentElement;
                        table_row.remove(table_row);
                    } else {
                        alert("You must remained at least one row in the table");
                    }

                }
            }
        }

        


    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>