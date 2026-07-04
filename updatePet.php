<?php

session_start();
include("connection.php");

if(isset($_POST['update']))
{

    $petID = $_POST['petID'];
    $petName = trim($_POST['petName']);
    $petType = trim($_POST['petType']);
    $breed = trim($_POST['breed']);
    $birthDate = $_POST['birthDate'];

    $sql = "UPDATE pet
            SET
                petName='$petName',
                petType='$petType',
                breed='$breed',
                birthDate='$birthDate'
            WHERE petID='$petID'";

    if(mysqli_query($conn,$sql))
    {
        $_SESSION['success'] = "Pet details updated successfully!";
    }
    else
    {
        $_SESSION['error'] = "Failed to update pet.";
    }

    header("Location: petDetails.php");
    exit();

}
else
{
    header("Location: petDetails.php");
    exit();
}

?>