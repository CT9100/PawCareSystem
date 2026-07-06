<?php
include 'connection.php';
if (isset($_GET['id'])) {
    $slotID = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM TimeSlot WHERE slotID=?");
    $stmt->bind_param("s", $slotID);
    $stmt->execute();
}
header("Location: time_slot.php");
?>