<?php
session_start();
include 'connection.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $serviceName = $_POST['serviceName'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];


    $insertQuery = "
    INSERT INTO grooming 
    (serviceName, description, duration, price)
    VALUES (?, ?, ?, ?)
    ";


   $stmt = $conn->prepare($insertQuery);

    if(!$stmt){
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param(
        "sssd",
        $serviceName,
        $description,
        $duration,
        $price
    );

    if($stmt->execute()){

        header("Location: service.php");
        exit();

    }
    else{

        $error = "Failed to add service: ".$conn->error;

    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<title>PawCare - Add Service</title>


<style>

body { 
    margin:0; 
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    background-color:#333; 
    color:white; 
    display:flex;
    flex-direction:column;
    height:100vh;
}


.navbar { 
    background-color:#5ce1e6; 
    padding:15px 30px; 
    display:flex; 
    align-items:center;
}


.logo { 
    font-weight:bold; 
    font-size:24px; 
    color:#333; 
    background:white; 
    padding:5px 15px; 
    border-radius:8px; 
}



.container {

flex-grow:1;
display:flex;
justify-content:center;
align-items:center;

}



.modal-content {

background:rgba(184,134,11,0.85);
padding:40px;
border-radius:20px;
width:500px;
display:flex;
flex-direction:column;
align-items:center;

}



.modal-title {

background:#ffc059;
padding:10px 50px;
border-radius:8px;
font-weight:bold;
margin-top:-60px;
margin-bottom:30px;
color:#333;
font-size:20px;

}



.form-group {

display:flex;
align-items:center;
width:100%;
margin-bottom:20px;

}



.form-label {

background:#1ccbf2;
padding:10px;
border-radius:8px;
width:120px;
text-align:center;
font-weight:bold;
margin-right:20px;
color:#333;

}



.form-input {

flex-grow:1;
background:transparent;
border:none;
border-bottom:2px dashed white;
color:white;
padding:10px;
font-size:16px;
outline:none;

}



textarea.form-input {

height:70px;
resize:none;

}



.btn-save {

background:#ccff66;
border:none;
padding:12px 50px;
border-radius:8px;
font-weight:bold;
cursor:pointer;
margin-top:20px;
font-size:16px;
width:100%;
color:#333;

}



.btn-cancel {

background:#ff6666;
padding:12px 50px;
border-radius:8px;
font-weight:bold;
cursor:pointer;
margin-top:10px;
font-size:16px;
color:white;
text-decoration:none;
text-align:center;
width:100%;
box-sizing:border-box;

}



</style>


</head>



<body>


<div class="navbar">

<div class="logo">
🐾 PawCare
</div>

</div>




<div class="container">


<form class="modal-content" 
action="add_service.php" 
method="POST">


<div class="modal-title">
Add Service
</div>



<?php

if(isset($error))
echo "<p style='color:#ffcccc;font-weight:bold;'>$error</p>";

?>



<div class="form-group">

<div class="form-label">
Service :
</div>

<input 
type="text"
name="serviceName"
class="form-input"
placeholder="Example: Full Grooming"
required>

</div>




<div class="form-group">

<div class="form-label">
Description :
</div>


<textarea
name="description"
class="form-input"
placeholder="Describe the service">
</textarea>


</div>





<div class="form-group">

<div class="form-label">
Duration :
</div>


<input 
type="text"
name="duration"
class="form-input"
placeholder="Example: 2 hours"
required>


</div>




<div class="form-group">

<div class="form-label">
Price :
</div>


<input 
type="number"
name="price"
step="0.01"
class="form-input"
placeholder="Example: 50.00"
required>


</div>




<button class="btn-save">
Save Service
</button>


<a href="service.php" class="btn-cancel">
Cancel
</a>



</form>


</div>



</body>

</html>