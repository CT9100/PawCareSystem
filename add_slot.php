<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $slotDate = $_POST['slotDate'];
    $slotTime = $_POST['slotTime'];
    $availability = "Available"; // Default status

    // Generate a simple unique slotID (e.g., T001, T002)
    // In a production app, you might query the DB to find the highest ID and +1 it.
    $randomNum = rand(100, 999);
    $slotID = "T" . $randomNum; 

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO TimeSlot (slotID, slotDate, slotTime, availability) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $slotID, $slotDate, $slotTime, $availability);

    if ($stmt->execute()) {
        echo "<script>alert('Slot added successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding slot: " . $conn->error . "'); window.location.href='admin_dashboard.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>