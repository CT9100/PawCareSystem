<?php

include 'connection.php';


$id=$_GET['id'];


$sql="
DELETE FROM grooming
WHERE serviceID='$id'
";


$conn->query($sql);


header("Location: service.php");


?>