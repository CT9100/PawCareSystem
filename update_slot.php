<?php
include 'connection.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $slotID = $_POST['slotID'];
    $slotDate = $_POST['slotDate'];
    $slotTime = $_POST['slotTime'];
    $availability = $_POST['availability'];

    $stmt = $conn->prepare("UPDATE TimeSlot SET slotDate=?, slotTime=?, availability=? WHERE slotID=?");
    $stmt->bind_param("ssss", $slotDate, $slotTime, $availability, $slotID);
    $stmt->execute();
    
    header("Location: time_slot.php");
}
?>