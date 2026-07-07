<?php

session_start();
include("connection.php");


if(!isset($_SESSION['customerID'])){
    header("Location: login.php");
    exit();
}


$petID = $_POST['petID'];
$serviceID = $_POST['serviceID'];
$slotID = $_POST['slotID'];
$bookingDate = $_POST['bookingDate'];
$status = "Pending";


// ================================
// Generate Appointment ID
// ================================

$idQuery = "
SELECT appointmentID 
FROM appointment 
ORDER BY appointmentID DESC 
LIMIT 1
";


$idResult = mysqli_query($conn, $idQuery);


if(mysqli_num_rows($idResult) > 0){

    $row = mysqli_fetch_assoc($idResult);

    $lastID = $row['appointmentID']; 
    // Example: A003


    $number = intval(substr($lastID,1));

    $nextNumber = $number + 1;


    $appointmentID = "A" . str_pad($nextNumber,3,'0',STR_PAD_LEFT);


}
else{

    $appointmentID = "A001";

}



// ================================
// Insert Booking
// ================================


$sql = "

INSERT INTO appointment

(appointmentID, petID, serviceID, slotID, status)

VALUES

(?,?,?,?,?)

";


$stmt = $conn->prepare($sql);

$stmt->bind_param(

"ssiss",

$appointmentID,
$petID,
$serviceID,
$slotID,
$status

);



if($stmt->execute()){


    // Update slot availability

    $update = "

    UPDATE timeslot

    SET availability='Booked'

    WHERE slotID=?

    ";


    $stmt2=$conn->prepare($update);

    $stmt2->bind_param("s",$slotID);

    $stmt2->execute();



    echo "

    <script>

    alert('Booking successful!');

    window.location='dashboard.php';

    </script>

    ";


}
else{

    echo "Booking failed: ".$stmt->error;

}


?>