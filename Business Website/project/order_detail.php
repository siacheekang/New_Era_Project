<?php
session_start();
include 'config/usersign.php';
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order detail info</title>
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
            <h1>Read Order</h1>
        </div>

        <!-- PHP read one record will be here -->
        <?php
        // get passed parameter value, in this case, the record ID
        // isset() is a PHP function used to verify if a value is there or not
        $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : die('ERROR: Record ID not found.');

       

        //include database connection
        include 'config/database.php';

        // read current record's data
        try {
            $query = "SELECT * FROM order_detail INNER JOIN order_summary ON order_detail.order_id=order_summary.order_id  
                      INNER JOIN customer ON order_summary.customer_id=customer.id WHERE order_detail.order_ID = :order_id ";

            $stmt = $con->prepare($query);

            $stmt->bindParam(":order_id", $order_id);

            if ($stmt->execute()) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $username_ = $row['username'];
                $order_date = $row['date'];
            }
        }
        // show error
        catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }
        ?>

        <table class='table table-hover table-responsive table-bordered' style="width: 50%;">
            <tr>
                <td>Username</td>
                <td><?php echo htmlspecialchars($username_, ENT_QUOTES);  ?></td>
            </tr>
            <tr>
                <td>Order ID</td>
                <td><?php echo htmlspecialchars($order_id, ENT_QUOTES);  ?></td>
            </tr>
            <tr>
                <td>Order Date</td>
                <td><?php echo htmlspecialchars($order_date, ENT_QUOTES);  ?></td>
            </tr>
            <tr>


                <table class='table table-hover table-responsive table-bordered text-center'>
                    <tr>
                        <td>No.</td>
                        <td>Product Name</td>
                        <td>Product Price (RM)</td>
                        <td>Product Quantity</td>
                        <td>Amount (RM)</td>
                    </tr>

                    <?php
                    $query1 = "SELECT * from order_detail WHERE order_id=:order_id";
                    $stmt = $con->prepare($query1);
                    $stmt->bindParam(":order_id", $order_id);
                    $stmt->execute();
                    $num = $stmt->rowCount();

                    if ($num > 0) {
                        $count = 1;
                        $total_amount = 0;
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $query2 = "SELECT * from products WHERE id =:productid";
                            $stmt1 = $con->prepare($query2);
                            $stmt1->bindParam(":productid",$row['product_id']);
                            $stmt1->execute();
                            $row2 = $stmt1->fetch(PDO::FETCH_ASSOC);
                            $price = $row2['promotion'] == 0 ? $row2['promotion'] : $row2['price'];
                            $amount = floatval($price) * intval($row['quantity']);
                            $total_amount += $amount;

                            echo "<tr>
                                        <td>" . $count . "</td>
                                        <td>" . $row2['name'] . "</td>
                                        <td>" . number_format((float)$price, 2,) . "</td>
                                        <td>" . $row['quantity'] . "</td>
                                        <td class='text-end'>" . number_format($amount, 2,) . "</td>
                                        </tr>";
                            $count++;
                        }
                    }
                    ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><strong>Raw price</strong></td>
                        <td class="text-end"><?php echo "<strong>RM" .$total_amount . "</strong>" ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><strong>Total price(rounded)</strong></td>
                        <td class="text-end"><?php echo "<strong>RM" . number_format(round($total_amount, 1),2) . "</strong>" ?></td>
                    </tr>

                </table>

            </tr>

            <tr>
                <td></td>
                <td>
                    <a href='order_summary.php' class='btn btn-danger'>Back to order summary</a>
                </td>
            </tr>
        </table>



    </div> <!-- end .container -->
 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>