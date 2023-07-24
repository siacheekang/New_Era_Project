<?php
session_start();
include 'config/usersign.php';
?>

<!DOCTYPE HTML>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Summary</title>
    <!-- Latest compiled and minified Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body style="background-color: #FFF8EA">
    <!-- container -->
    <?php
    include 'config/navbar.php';
    ?>
    <div class="container">
        <div class="page-header">
            <h1>Read Order</h1>
        </div>
        

        <?php
        // include database connection

        include 'config/database.php';

        // delete message prompt will be here
        $action = isset($_GET['action']) ? $_GET['action'] : "";

        // if it was redirected from delete.php
        if ($action == 'deleted') {
            echo "<div class='alert alert-success'>Record was deleted.</div>";
        }

        if ($action == 'cancel') {
            echo "<div class='alert alert-danger'>There is record in order</div>";
        }

        if ($action == 'successful') {
            echo "<div class='alert alert-success'>Record was saved.</div>";
        }

        // select all data
        $query = "SELECT smry.order_id, smry.date, cus.username, cus.firstname, cus.lastname, SUM(pdc.price*detai.quantity) AS total
        FROM order_summary AS smry
        INNER JOIN customer AS cus
        ON smry.customer_id = cus.id
        INNER JOIN order_detail AS detai
        ON detai.order_id = smry.order_id
        INNER JOIN products AS pdc
        ON detai.product_id = pdc.id
        GROUP BY smry.order_id
        ORDER BY order_id DESC";


        $stmt = $con->prepare($query);
        $stmt->execute();

        // this is how to get number of rows returned
        $num = $stmt->rowCount();

        // link to create record form
        echo "<a href='order_create.php' class='btn btn-primary mb-3'>Create New Order</a>";

        //check if more than 0 record found
        if ($num > 0) {


            echo "<table class='table table-hover table-responsive table-bordered'>"; //start table

            //creating our table heading
            echo "<tr>";
            echo "<th>Order ID</th>";
            echo "<th>Customer Name</th>";
            echo "<th>Fistname</th>";
            echo "<th>Lastname</th>";
            echo "<th>Totalprice</th>";
            echo "<th>Date</th>";
            echo "<th>Action</th>";
            echo "</tr>";
            // retrieve our table contents
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // extract row
                // this will make $row['firstname'] to just $firstname only


                extract($row);
                // creating new table row per record
                echo "<tr>";
                echo "<td>{$order_id}</td>";
                echo "<td>{$username}</td>";
                echo "<td>{$firstname}</td>";
                echo "<td>{$lastname}</td>";
                echo "<td>" . number_format(round($total, 1,), 2) . "</td>";
                echo "<td>{$date}</td>";
                echo "<td>";
                // read one record
                echo "<a href='order_detail.php?order_id={$order_id}' class='btn btn-outline-info mx-2'>Read</a>";

                // we will use this links on next part of this post
                echo "<a href='order_update.php?order_id={$order_id}' class='btn btn-outline-primary mx-2'>Edit</a>";

                // we will use this links on next part of this post
                echo "<a href='#' onclick='delete_order({$order_id});'  class='btn btn-outline-danger mx-2'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }


            // end table
            echo "</table>";
        } else {
            echo "<div class='alert alert-danger'>No records found.</div>";
        }
        ?>


    </div> <!-- end .container -->

    <!-- confirm delete record will be here -->
    <script type='text/javascript'>
        // confirm record deletion
        function delete_order(order_id) {

            if (confirm('Are you sure to delete?')) {
                // if user clicked ok,
                // pass the id to delete.php and execute the delete query
                window.location = 'order_delete.php?order_id=' + order_id;
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>