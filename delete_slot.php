<<<<<<< HEAD
<?php
include 'connection.php';
if (isset($_GET['id'])) {
    $slotID = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM TimeSlot WHERE slotID=?");
    $stmt->bind_param("s", $slotID);
    $stmt->execute();
}
header("Location: time_slot.php");
=======
<?php
include 'connection.php';
if (isset($_GET['id'])) {
    $slotID = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM TimeSlot WHERE slotID=?");
    $stmt->bind_param("s", $slotID);
    $stmt->execute();
}
header("Location: time_slot.php");
>>>>>>> ebf7df621d31b17e7565d6e987cc0bc185c8bdb3
?>