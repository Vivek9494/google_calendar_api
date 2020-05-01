<?php 
session_start();
include "../connection.php";

if(empty($_SESSION['user'])){
    header('location:../index.php');
}
if(!empty($_GET)){
    $event_id = $_GET['event_id'];
    $action = $_GET['action'];

    $sql = "SELECT * FROM events WHERE id='$event_id'";
    $query = mysqli_query($conn,$sql);
    $rows = mysqli_num_rows($query);
    if($rows == 0){
        echo "Event doesn't exist.";die;
    }else{
        $event = mysqli_fetch_array($query);
        $sql1 = "UPDATE events SET status = '$action' WHERE id='$event_id'";
        $query = mysqli_query($conn,$sql1);
        
        header('Location:events.php');
    }
}
?>