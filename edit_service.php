<?php
session_start();
include 'connection.php';


// Get service ID from URL
if(isset($_GET['id'])){

    $serviceID = $_GET['id'];

    $query = "SELECT * FROM grooming WHERE serviceID=?";

    $stmt = $conn->prepare($query);

    $stmt->bind_param("i", $serviceID);

    $stmt->execute();

    $result = $stmt->get_result();

    $service = $result->fetch_assoc();

}
else{

    header("Location: service.php");
    exit();

}


?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<title>PawCare - Edit Service</title>


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



.btn-update {

background:#8be763;
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
action="update_service.php" 
method="POST">


<div class="modal-title">
Edit Service
</div>



<input type="hidden" 
name="serviceID"
value="<?php echo $service['serviceID']; ?>">





<div class="form-group">

<div class="form-label">
Service :
</div>


<input 
type="text"
name="serviceName"
class="form-input"
value="<?php echo htmlspecialchars($service['serviceName']); ?>"
required>

</div>





<div class="form-group">

<div class="form-label">
Description :
</div>


<textarea
name="description"
class="form-input"><?php echo htmlspecialchars($service['description']); ?></textarea>


</div>






<div class="form-group">

<div class="form-label">
Duration :
</div>


<input 
type="text"
name="duration"
class="form-input"
value="<?php echo htmlspecialchars($service['duration']); ?>"
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
value="<?php echo $service['price']; ?>"
required>


</div>






<button type="submit" class="btn-update">
Update Service
</button>



<a href="service.php" class="btn-cancel">
Cancel
</a>




</form>


</div>



</body>

</html>