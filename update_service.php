<?php

include 'connection.php';


$id=$_POST['serviceID'];
$name=$_POST['serviceName'];
$description=$_POST['description'];
$duration=$_POST['duration'];
$price=$_POST['price'];



$sql="
UPDATE grooming SET

serviceName='$name',
description='$description',
duration='$duration',
price='$price'

WHERE serviceID='$id'

";


if($conn->query($sql))
{

header("Location: service.php");

}

?>