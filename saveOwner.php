<?php
    session_start();

    include("database/connection.php");

    $name=$_POST['name'];
    $phone=$_POST['phone'];
    $email=$_POST['email'];
    $password=$_POST['password'];
    $address=$_POST['address'];

    $result=mysqli_query($conn,
    "SELECT customerID
    FROM customer
    ORDER BY customerID DESC
    LIMIT 1");

    if(mysqli_num_rows($result)>0){
        $row=mysqli_fetch_assoc($result);
        $num=substr($row['customerID'],1);
        $num++;
        $customerID="C".str_pad($num,3,"0",STR_PAD_LEFT);
    }

    else{
        $customerID="C001";
    }

    $sql="INSERT INTO customer
    VALUES
    ('$customerID',
    '$name',
    '$email',
    '$phone',
    '$password',
    '$address')";

    if(mysqli_query($conn,$sql)){
        $_SESSION['customerID']=$customerID;
        header("Location: petDetails.php");
    }

    else{
        echo "Error : ".mysqli_error($conn);
    }
?>