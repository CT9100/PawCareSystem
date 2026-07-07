<?php

include 'connection.php';


$name=$_POST['serviceName'];
$description=$_POST['description'];
$duration=$_POST['duration'];
$price=$_POST['price'];



$sql="
INSERT INTO grooming
(serviceName,description,duration,price)

VALUES

('$name','$description','$duration','$price')

";



if($conn->query($sql))
{

header("Location: service.php");

}
else
{

echo "Error: ".$conn->error;

}


?>