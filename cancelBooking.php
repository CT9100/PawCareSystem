<?php
session_start();
include("connection.php");

if(!isset($_SESSION['customerID'])){
    header("Location: login.php");
    exit();
}

if(isset($_GET['appointmentID'])){
    $appointmentID = mysqli_real_escape_string($conn, $_GET['appointmentID']);
    $customerID = $_SESSION['customerID'];

    // Check that this appointment belongs to this customer
    $check_sql = "
        SELECT a.appointmentID, a.slotID
        FROM appointment a
        JOIN pet p ON a.petID = p.petID
        WHERE a.appointmentID='$appointmentID'
        AND p.customerID='$customerID'
    ";
    $check_result = mysqli_query($conn, $check_sql);
    if(mysqli_num_rows($check_result) > 0){
        $data = mysqli_fetch_assoc($check_result);
        $slotID = $data['slotID'];

        // 1. Update appointment status
        $update_app = "
            UPDATE appointment
            SET status='Cancelled'
            WHERE appointmentID='$appointmentID'
        ";
        mysqli_query($conn, $update_app);

        // 2. Return slot availability
        $update_slot = "
            UPDATE timeslot
            SET availability='Available'
            WHERE slotID='$slotID'
        ";
        mysqli_query($conn, $update_slot);
    }
}
header("Location: dashboard.php");
exit();
?>