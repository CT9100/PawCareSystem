<?php
session_start();
include("connection.php");

if(isset($_POST['save']))
{
    $customerID = $_POST['customerID'];
    $petName    = trim($_POST['petName']);
    $petType    = trim($_POST['petType']);
    $breed      = trim($_POST['breed']);
    $birthDate  = $_POST['birthDate'];

    // Generate petID
    $query = "SELECT petID FROM pet ORDER BY petID DESC LIMIT 1";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0)
    {
        $row = mysqli_fetch_assoc($result);
        $lastID = $row['petID'];

        $number = intval(substr($lastID,1));
        $number++;

        $petID = "P".str_pad($number,3,"0",STR_PAD_LEFT);
    }
    else
    {
        $petID = "P001";
    }

    // INSERT
    $sql = "INSERT INTO pet (petID, petName, petType, breed, birthDate, customerID)
            VALUES ('$petID', '$petName', '$petType', '$breed', '$birthDate', '$customerID')";

    if(mysqli_query($conn,$sql))
    {
        $_SESSION['success'] = "Pet added successfully! 🐶";
    }
    else
    {
        $_SESSION['error'] = "Failed to add pet.";
    }

    // 🔥 IMPORTANT: ALWAYS REDIRECT BACK
    header("Location: petDetails.php");
    exit();
}
else
{
    header("Location: petDetails.php");
    exit();
}
?>