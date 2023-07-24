<!DOCTYPE HTML>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Homepage</title>
  <style>
    .hovercolor:hover{ color: white !important; }
    .hoverclr:hover{color: black !important;}
    .lineside{border: 0.2px solid black !important;}
  </style>
  <!-- Latest compiled and minified Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body style="background-color: #FFF8EA">

  <?php
  include 'config/navbar.php';
  include 'config/database.php';
  ?>

  <div class="container my-5">


    <?php
    echo "<h1 class='text-center'> Worldwide Business</h1>";
    echo "<h1 class='text-center'>" . date('dS F Y') . "</h1>";

    ?>


  </div>

  <div class="container mb-3">
    <div class="row">
      <div class="col-sm-4">
        <div class="card text-bg-secondary">
          <div class="card-body">
            <a href="customer_read.php" style="font-size:large" class="card-title text-white text-decoration-none">Total number of customer:</a>
            <p class="card-text text-center">

              <!-- display total customer -->
              <?php
              $query = "SELECT COUNT(*) AS total_customer
              FROM customer AS cus";

              $stmt = $con->prepare($query);
              $stmt->execute();
              $num = $stmt->rowCount();
              $row = $stmt->fetch(PDO::FETCH_ASSOC);
              extract($row);
              echo $total_customer;
              ?>
            </p>
          </div>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="card text-bg-secondary">
          <div class="card-body">
          <a href="product_read.php" style="font-size:large" class="card-title text-white text-decoration-none">Total number of product:</a>
            <p class="card-text text-center">
              <!-- display total product -->
              <?php
              $query1 = "SELECT  COUNT(*) AS total_product
              FROM products AS prod";

              $stmt1 = $con->prepare($query1);
              $stmt1->execute();
              $num1 = $stmt1->rowCount();
              $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
              extract($row1);
              echo $total_product;
              ?>
            </p>
          </div>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="card text-bg-secondary">
          <div class="card-body">
          <a href="order_summary.php" style="font-size:large" class="card-title text-white text-decoration-none">Total number of order:</a>
            <p class="card-text text-center">
              <!-- display total order -->
              <?php
              $query2 = "SELECT  COUNT(*) AS total_order
              FROM order_summary AS smry";

              $stmt2 = $con->prepare($query2);
              $stmt2->execute();
              $num2 = $stmt2->rowCount();
              $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
              extract($row2);
              echo $total_order;
              ?>
            </p>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- display latest order id and summary -->
  <div class="container">
    <?php
    $query = "SELECT smry.order_id, smry.date, cus.username, SUM(pdc.price*detai.quantity) AS total
        FROM order_summary AS smry
        INNER JOIN customer AS cus
        ON smry.customer_id = cus.id
        INNER JOIN order_detail AS detai
        ON detai.order_id = smry.order_id
        INNER JOIN products AS pdc
        ON detai.product_id = pdc.id
        GROUP BY smry.order_id
        ORDER BY order_id DESC LIMIT 1";


    $stmt = $con->prepare($query);
    $stmt->execute();

    // this is how to get number of rows returned
    $num = $stmt->rowCount();

    //check if more than 0 record found
    if ($num > 0) {


      echo "<table class='table table-responsive table-bordered'>"; //start table
      echo "Latest Order ID & Summary";
      //creating our table heading
      echo "<tr>";
      echo "<th>Order ID</th>";
      echo "<th>Customer Name</th>";
      echo "<th>Totalprice</th>";
      echo "<th>Date</th>";

      echo "</tr>";
      // retrieve our table contents
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // extract row
        // this will make $row['firstname'] to just $firstname only


        extract($row);
        // creating new table row per record
        echo "<tr>";
        echo "<td ><a href='order_detail.php?order_id={$order_id}'  class='text-decoration-none text-dark hovercolor'>{$order_id}</a></td>";
        // echo "<td>{$order_id}</td>";
        echo "<td>{$username}</td>";
        echo "<td>" . "RM" . number_format(round($total, 1,), 2) . "</td>";
        echo "<td>{$date}</td>";
        echo "</tr>";
      }


      // end table
      echo "</table>";
    } else {
      echo "<div class='alert alert-danger'>No records found.</div>";
    }
    ?>
  </div>

  <!-- display highest amount money purchased -->
  <div class="container">
    <?php

    $query = "SELECT smry.order_id, smry.date, cus.username, SUM(pdc.price*detai.quantity) AS total
    FROM order_summary AS smry
    INNER JOIN customer AS cus
    ON smry.customer_id = cus.id
    INNER JOIN order_detail AS detai
    ON detai.order_id = smry.order_id
    INNER JOIN products AS pdc
    ON detai.product_id = pdc.id
    GROUP BY smry.order_id
    ORDER BY SUM(pdc.price*detai.quantity) DESC Limit 1";

    $stmt = $con->prepare($query);
    $stmt->execute();

    // this is how to get number of rows returned
    $num = $stmt->rowCount();

    //check if more than 0 record found
    if ($num > 0) {


      echo "<table class='table table-responsive table-bordered'>"; //start table
      echo "Latest Order ID & Summary(With highest purchased amount)";
      //creating our table heading
      echo "<tr>";
      echo "<th>Order ID</th>";
      echo "<th>Customer Name</th>";
      echo "<th>Totalprice</th>";
      echo "<th>Date</th>";

      echo "</tr>";
      // retrieve our table contents
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // extract row
        // this will make $row['firstname'] to just $firstname only


        extract($row);
        // creating new table row per record
        echo "<tr>";
        echo "<td ><a href='order_detail.php?order_id={$order_id}'  class='text-decoration-none text-dark hovercolor'>{$order_id}</a></td>";
        echo "<td>{$username}</td>";
        echo "<td>" . "RM" . number_format(round($total, 1,), 2) . "</td>";
        echo "<td>{$date}</td>";
        echo "</tr>";
      }


      // end table
      echo "</table>";
    } else {
      echo "<div class='alert alert-danger'>No records found.</div>";
    }
    ?>
  </div>

  <div class="container mb-3">
    <div class="row">
      <div class="col-sm-6">
        <div class="card text-bg-secondary">
          <div class="card-body">
            <h5 class="card-title">Total 5 selling products:</h5>
            <p class="card-text text-center">
              <!-- Top 5 selling product -->
              <?php
              $query = "SELECT  prod.name, prod.id, detai.product_id, SUM(detai.quantity) AS best_product
              FROM products AS prod
              INNER JOIN order_detail AS detai
              WHERE prod.id = detai.product_id
              GROUP BY prod.name
              ORDER BY best_product DESC
              LIMIT 5";

              $stmt = $con->prepare($query);
              $stmt->execute();
              $num = $stmt->rowCount();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                echo "<table table-responsive table-bordered'>";
                echo "<td>";
                echo "<td ><a href='product_read_one.php?id={$id}'  class='text-decoration-none text-white hoverclr'>{$name}</a></td>";
                echo "</td>";
                echo "</table>";
              }
              ?>
            </p>
          </div>
        </div>
      </div>

      <div class="col-sm-6">
        <div class="card text-bg-secondary">
          <div class="card-body">
            <h5 class="card-title">Products that never purchased by any customer:</h5>
            <p class="card-text text-center">
              <!-- 3 product no one want -->
              <?php
              $query = "SELECT  prod.name, prod.id, detai.product_id, SUM(detai.quantity) AS non_product
              FROM products AS prod
              LEFT JOIN order_detail AS detai
              ON prod.id = detai.product_id 
              WHERE detai.product_id is null
              GROUP BY prod.name
              LIMIT 3";

              $stmt = $con->prepare($query);
              $stmt->execute();
              $num = $stmt->rowCount();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                echo "<table table-responsive table-bordered'>";
                echo "<td>";
                echo "<td ><a href='product_read_one.php?id={$id}'  class='text-decoration-none text-white hoverclr'>{$name}</a></td>";
                echo "</td>";
                echo "</table>";
              }
              ?>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- end .container -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>