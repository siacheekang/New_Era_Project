<?php
// include database connection
include 'config/database.php';
try {     
    // get record ID
    // isset() is a PHP function used to verify if a value is there or not
    $id=isset($_GET['order_id']) ? $_GET['order_id'] :  die('ERROR: Record ID not found.');

    // delete query
    $query = "DELETE FROM order_summary WHERE order_id = ?";
    $query1 = "DELETE FROM order_detail WHERE order_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bindParam(1, $id);
    $stmt1 = $con->prepare($query1);
    $stmt1->bindParam(1, $id);
    $stmt1->execute();
    if($stmt->execute()){
        // redirect to read records page and
        // tell the user record was deleted
        header('Location: order_summary.php?action=deleted');
    }else{
        die('Unable to delete record.');
    }
}
// show error
catch(PDOException $exception){
    die('ERROR: ' . $exception->getMessage());
}
?>