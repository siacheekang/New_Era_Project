<?php
// include database connection
include 'config/database.php';
try {     
    // get record ID
    // isset() is a PHP function used to verify if a value is there or not
    $id=isset($_GET['id']) ? $_GET['id'] :  die('ERROR: Record ID not found.');

    $query_1 = "SELECT * FROM order_detail WHERE product_id = ?";
    $stmt_1 = $con->prepare($query_1);
    $stmt_1->bindParam(1, $id);
    $stmt_1->execute();
    $num = $stmt_1->rowCount();
    if ($num > 0) {
        header('Location: order_summary.php?action=cancel');
    }else{
        $delete_image="SELECT image from products WHERE id = ?";
        $stmt2 = $con->prepare($delete_image);
        $stmt2->bindParam(1, $id);
        $stmt2->execute();
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);
        $image=$row['image'];

    // delete query
    $query = "DELETE FROM products WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bindParam(1, $id);
     
    if($stmt->execute()){
        // redirect to read records page and
        // tell the user record was deleted
        if($image!=""){
        unlink("uploads_prod/$image");
        }
        header('Location: product_read.php?action=deleted');
    }else{
        
        die('Unable to delete record.');
    }
}
}
// show error
catch(PDOException $exception){
    die('ERROR: ' . $exception->getMessage());
}
?>
