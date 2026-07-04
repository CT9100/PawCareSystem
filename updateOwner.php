<?php

session_start();
include("connection.php");

// Check if form is submitted
if(isset($_POST['save']))
{
    // Retrieve form data
    $customerID = $_POST['customerID'];
    $name       = $_POST['name'];
    $phone      = $_POST['phone'];
    $email      = $_POST['email'];
    $password   = $_POST['password'];
    $address    = $_POST['address'];

    // Update customer information
    $sql = "UPDATE customer SET
            name='$name',
            phone='$phone',
            email='$email',
            password='$password',
            address='$address'
            WHERE customerID='$customerID'";

    $result = mysqli_query($conn, $sql);

    if($result)
    {
        // Update session name if it is displayed in navbar
        $_SESSION['customerName'] = $name;

        echo "<script>
                alert('Owner details updated successfully!');
                window.location='petDetails.php';
              </script>";
    }
    else
    {
        echo "<script>
                alert('Update failed!');
                window.history.back();
              </script>";

        echo mysqli_error($conn);
    }
}
else
{
    header("Location: ownerDetails.php");
}

?>