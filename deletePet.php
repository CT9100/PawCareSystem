<?php
    session_start();
    include("connection.php");

    if(isset($_GET['id']))
    {
        $petID = $_GET['id'];
        $sql = "DELETE FROM pet WHERE petID='$petID'";
        if(mysqli_query($conn,$sql))
        {
            $_SESSION['success'] = "Pet deleted successfully!";
        }
        else
        {
            $_SESSION['error'] = "Failed to delete pet.";
        }
    }
    header("Location: petDetails.php");
    exit();
?>