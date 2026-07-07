<?php
include 'connection.php';

$query = "SELECT * FROM grooming ORDER BY serviceID DESC";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html>
<head>

<title>PawCare - Services</title>

<style>

body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #333; color: white; }
.navbar { background-color: #5ce1e6; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; color: white; }
.logo { font-weight: bold; font-size: 24px; color: #333; background: white; padding: 5px 15px; border-radius: 8px; }
.nav-links a { color: white; text-decoration: none; margin: 0 15px; font-size: 18px; }
.nav-links a.active { border-bottom: 2px solid white; padding-bottom: 5px; }
.user-profile { display: flex; align-items: center; gap: 10px; font-weight: bold; color: #333; }
.container{
    padding:40px;
}


.title{
    color:white;
    font-size:30px;
    margin-bottom:20px;
}


.btn-add{

    background:#ccff66;
    padding:12px 25px;
    border-radius:10px;
    text-decoration:none;
    color:#333;
    font-weight:bold;

}


/* Data Grid Styles */
.grid-headers { 
    display: grid; 
    grid-template-columns: repeat(5, 1fr); 
    gap: 15px; 
    margin-bottom: 15px; 
    text-align: center; 
}

.header-cell { 
    background-color: #ffc059; 
    padding: 15px; 
    border-radius: 8px; 
    font-weight: bold; 
}


.grid-row { 
    display: grid; 
    grid-template-columns: repeat(5, 1fr); 
    gap: 15px; 
    margin-bottom: 15px; 
    text-align: center; 
    align-items: center; 
}


.data-cell { 
    background-color: #1ccbf2; 
    padding: 15px; 
    border-radius: 8px; 
    color: #333; 
    font-weight: 500; 
    box-sizing: border-box;
}


/* Action Buttons */
.action-btns { 
    display: flex; 
    gap: 10px; 
    width: 100%; 
}


.btn-edit, .btn-delete { 
    flex: 1;
    padding: 15px 0;
    border-radius: 8px;
    font-weight: bold;
    text-align: center;
    text-decoration: none;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}


.btn-edit { 
    background:#8be763;
    color:#333;
}


.btn-delete { 
    background:#ff6666;
    color:white;
}

.btn-logout { 
    color: red; 
    text-decoration: none; 
    margin: 0 15px; 
    font-size: 18px; 
    font-weight: bold; 
}   
.controls {
    display:flex;
    justify-content:flex-end;
    margin-bottom:30px;
    padding:0 10px;
}
</style>
</head>
<body>
<div class="navbar">
        <div class="logo">🐾 PawCare</div>
        <div class="nav-links">
            <a href="admin_dashboard.php">Booking Record</a>
            <a href="service.php" class="active" >Services</a>
            <a href="time_slot.php">Time Slots</a>
            <a href="report.php">Report</a>
        </div>
        <div class="user-profile">
        <a href="logout.php" class="btn-logout" onclick="return confirm('Are you sure you want to log out?')">Logout</a>
        <div style="background: black; width: 35px; height: 35px; border-radius: 50%;"></div>
    </div>
</div>

<div class="container">

<div class="title">
Manage Services
</div>


<div class="controls">
    <a href="add_service.php" class="btn-add">
    + Add Service
    </a>
</div>

<div class="grid-headers">

    <div class="header-cell">Service Name</div>
    <div class="header-cell">Description</div>
    <div class="header-cell">Duration</div>
    <div class="header-cell">Price</div>
    <div class="header-cell">Action</div>

</div>


<div id="service-container">

<?php

while($row=$result->fetch_assoc()){

echo '

<div class="grid-row">

    <div class="data-cell">
        '.$row['serviceName'].'
    </div>


    <div class="data-cell">
        '.$row['description'].'
    </div>


    <div class="data-cell">
        '.$row['duration'].' min
    </div>


    <div class="data-cell">
        RM '.$row['price'].'
    </div>


    <div class="action-btns">

        <a class="btn-edit"
        href="edit_service.php?id='.$row['serviceID'].'">
        Edit
        </a>


        <a class="btn-delete"
        href="delete_service.php?id='.$row['serviceID'].'"
        onclick="return confirm(\'Delete this service?\')">
        Delete
        </a>
    </div>
</div>
';
}
?>
</div>
</div>
</body>
</html>
