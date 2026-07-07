<?php
session_start();
include 'connection.php';

$selectedCustomer = isset($_GET['customerID']) ? $_GET['customerID'] : "";
$reportResult = null;
$totalSpent = 0;

if($selectedCustomer != "")
{
    $selectedCustomer = mysqli_real_escape_string($conn,$selectedCustomer);
    $reportQuery = "
    SELECT
        c.name,
        c.email,
        c.phone,
        c.address,
        p.petName,
        p.petType,
        g.serviceName,
        g.price,
        t.slotDate,
        t.slotTime,
        a.status
    FROM appointment a
    JOIN pet p
    ON a.petID = p.petID
    JOIN customer c
    ON p.customerID = c.customerID
    JOIN grooming g
    ON a.serviceID = g.serviceID
    JOIN timeslot t
    ON a.slotID = t.slotID
    WHERE c.customerID='$selectedCustomer'
    ORDER BY t.slotDate DESC";
    $reportResult = mysqli_query($conn,$reportQuery);
    $totalQuery="
    SELECT SUM(g.price) AS total
    FROM appointment a
    JOIN grooming g
    ON a.serviceID=g.serviceID
    JOIN pet p
    ON a.petID=p.petID
    WHERE
    p.customerID='$selectedCustomer'
    AND
    a.status='Completed'
    ";

    $totalResult=mysqli_query($conn,$totalQuery);
    $totalSpent=mysqli_fetch_assoc($totalResult)['total'];

    if(!$totalSpent)
        $totalSpent=0;
}

$revenueQuery = "SELECT SUM(g.price) AS total_revenue 
                 FROM Appointment a 
                 JOIN Grooming g ON a.serviceID = g.serviceID 
                 WHERE a.status = 'Completed'";
$revResult = $conn->query($revenueQuery);
$totalRevenue = ($revResult && $revResult->num_rows > 0) ? $revResult->fetch_assoc()['total_revenue'] : 0.00;
if(!$totalRevenue) $totalRevenue = 0.00;

$customerQuery = "SELECT name FROM Customer ORDER BY name ASC LIMIT 5";
$customerResult = $conn->query($customerQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PawCare - Report</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #333; color: white; }
        .navbar { background-color: #5ce1e6; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-weight: bold; font-size: 24px; color: #333; background: white; padding: 5px 15px; border-radius: 8px; }
        .nav-links a { color: white; text-decoration: none; margin: 0 15px; font-size: 18px; }
        .nav-links a.active { border-bottom: 2px solid white; padding-bottom: 5px; }
        .user-profile { display: flex; align-items: center; gap: 10px; font-weight: bold; }
        .report-container{width:90%; margin:40px auto; display:block;}
        .data-box {
            background: rgba(184, 134, 11, 0.7); /* Transparent brown */
            padding: 30px; border-radius: 15px; width: 250px; text-align: center;
            min-height: 300px;
        }
        .data-box h3 { margin-top: 0; margin-bottom: 30px; }
        .data-line { border-bottom: 2px dashed white; margin: 20px 0; padding-bottom: 10px; font-size: 18px; }
        .chart-placeholder {font-size: 100px; color: white; align-self: center; margin-left: 50px;}
        @media print{

    body{
        background:white;
        color:black;
    }

    body *{
        visibility:hidden;
    }

    #printArea,
    #printArea *{
        visibility:visible;
    }

    #printArea{
        position:absolute;
        left:0;
        top:0;
        width:100%;
        margin:0;
        padding:20px;
        box-shadow:none;
        color:black;
        background:white;
    }

    .btn-print{
        display:none !important;
    }

}
        

        .btn-logout { color: red; text-decoration: none; margin: 0 15px; font-size: 18px; font-weight: bold; }
    .search-card{
        background:white;
        color:#333;
        padding:30px;
        border-radius:15px;
        margin:auto;
        margin-top:40px;
        width:90%;
        box-shadow:0 5px 20px rgba(0,0,0,.15);
        }

        .search-card h2{
        margin-top:0;
        color:#28a9c9;
        }

        .search-row{
        display:flex;
        gap:15px;
        margin-top:20px;
        }

        .search-input{
        flex:1;
        padding:15px;
        font-size:16px;
        border-radius:10px;
        border:1px solid #ccc;
        outline:none;
        }

        .btn-search{
        background:#1ccbf2;
        color:white;
        border:none;
        padding:15px 35px;
        border-radius:10px;
        cursor:pointer;
        font-weight:bold;
        }

        .customer-card{
        background:white;
        margin:auto;
        width:90%;
        margin-top:30px;
        padding:30px;
        border-radius:15px;
        box-shadow:0 5px 20px rgba(0,0,0,.15);
        color:#333;
        }

        .customer-card table{
        width:100%;
        border-collapse:collapse;
        }

        .customer-card th{
        background:#5ce1e6;
        padding:15px;
        }

        .customer-card td{
        padding:15px;
        border-bottom:1px solid #ddd;
        }

        .btn-report{
        background:#8be763;
        padding:10px 18px;
        border-radius:8px;
        text-decoration:none;
        color:#333;
        font-weight:bold;
        }
        .btn-report:hover{
        background:#73d94f;
        }

        .report-box{
            background:white;
            margin:40px auto;
            width:90%;
            padding:40px;
            border-radius:15px;
            color:#333;
            box-shadow:0 5px 20px rgba(0,0,0,.15);
        }

        .report-header{
            text-align:center;
            margin-bottom:30px;
        }

        .report-header img{
            width:120px;
            margin-bottom:10px;
        }

        .owner-info{
            background:#f7f7f7;
            padding:20px;
            border-radius:10px;
            margin-bottom:30px;
        }

        .report-table{
            width:100%;
            border-collapse:collapse;
        }

        .report-table th{
            background:#5ce1e6;
            padding:15px;
        }

        .report-table td{
            padding:15px;
            border-bottom:1px solid #ddd;
        }

        .total-box{
            margin-top:30px;
            font-size:22px;
            text-align:right;
        }

        .btn-print{
            margin-top:25px;
            background:#1ccbf2;
            color:white;
            padding:15px 35px;
            border:none;
            border-radius:10px;
            cursor:pointer;
            float:right;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">🐾 PawCare</div>
            <div class="nav-links">
                <a href="admin_dashboard.php">Booking Record</a>
                <a href="service.php">Services</a>
                <a href="time_slot.php">Time Slots</a>
                <a href="report.php" class="active">Report</a>
            </div>
            <div class="user-profile">
                <a href="logout.php" class="btn-logout" onclick="return confirm('Are you sure you want to log out?')">Logout</a>
            <div style="background: black; width: 35px; height: 35px; border-radius: 50%;"></div>
        </div>
    </div>
    
    <div class="report-container">
    <div class="search-card">
        <h2>🐾 Customer Report</h2>
        <p>Select a customer by searching their name, email or phone number.</p>
        <form method="GET" action="report.php">
            <div class="search-row">
                <input
                    type="text"
                    name="keyword"
                    class="search-input"
                    placeholder="Search customer name, email or phone..."
                    value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">

                <button class="btn-search"> 🔍 Search </button>
            </div>
        </form>
    </div>
<?php

$keyword = "";

if(isset($_GET['keyword']))
{
    $keyword = trim($_GET['keyword']);
}

if($keyword != "")
{
    $keyword = mysqli_real_escape_string($conn,$keyword);
    $searchCustomer = mysqli_query($conn,
    "SELECT *
    FROM customer
    WHERE
    name LIKE '%$keyword%'
    OR
    email LIKE '%$keyword%'
    OR
    phone LIKE '%$keyword%'
    ORDER BY name");
?>

<div class="customer-card">

<h3>Search Result</h3>

<table>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Action</th>
    </tr>

    <?php
        if(mysqli_num_rows($searchCustomer)>0)
        { while($customer=mysqli_fetch_assoc($searchCustomer))
        {
    ?>

    <tr>
        <td>
            <?php echo $customer['name']; ?>
        </td>

        <td>
            <?php echo $customer['email']; ?>
        </td>

        <td>
            <?php echo $customer['phone']; ?>
        </td>

        <td>
            <a class="btn-report"
            href="report.php?customerID=<?php echo $customer['customerID']; ?>&keyword=<?php echo urlencode($keyword); ?>">
                Generate Report
            </a>
        </td>
    </tr>
    <?php
    }
    }
    else
    {
    ?>
    <tr>
    <td colspan="4">
    No customer found.
    </td>
    </tr>
    <?php
    }
    ?>
    </table>

    <?php } ?>

    <?php
        if($reportResult && mysqli_num_rows($reportResult)>0)
        {
        $owner=mysqli_fetch_assoc($reportResult);
    ?>

    <div class="report-box" id="printArea">
    <div class="report-header">
    <img src="images/paw.png">
    <h1>PawCare Grooming Report</h1>
    </div>
    <div class="owner-info">
    <h3>Owner Information</h3>
    <p><b>Name :</b> <?php echo $owner['name']; ?></p>
    <p><b>Email :</b> <?php echo $owner['email']; ?></p>
    <p><b>Phone :</b> <?php echo $owner['phone']; ?></p>
    <p><b>Address :</b> <?php echo $owner['address']; ?></p>
    </div>
    <table class="report-table">
    <tr>
    <th>Pet</th>
    <th>Type</th>
    <th>Service</th>
    <th>Date</th>
    <th>Time</th>
    <th>Status</th>
    <th>Price</th>
    </tr>
    <tr>
    <td><?php echo $owner['petName']; ?></td>
    <td><?php echo $owner['petType']; ?></td>
    <td><?php echo $owner['serviceName']; ?></td>
    <td><?php echo date("d M Y",strtotime($owner['slotDate'])); ?></td>
    <td><?php echo date("h:i A",strtotime($owner['slotTime'])); ?></td>
    <td><?php echo $owner['status']; ?></td>
    <td>RM <?php echo number_format($owner['price'],2); ?></td>
    </tr>
    <?php
        while($row=mysqli_fetch_assoc($reportResult))
        {
    ?>
    <tr>
        <td><?php echo $row['petName']; ?></td>
        <td><?php echo $row['petType']; ?></td>
        <td><?php echo $row['serviceName']; ?></td>
        <td><?php echo date("d M Y",strtotime($row['slotDate'])); ?></td>
        <td><?php echo date("h:i A",strtotime($row['slotTime'])); ?></td>
        <td><?php echo $row['status']; ?></td>
        <td>RM <?php echo number_format($row['price'],2); ?></td>
    </tr>
    <?php
    }
    ?>
    </table>
    <div class="total-box">
    Total Completed Spending :
    <b>
    RM <?php echo number_format($totalSpent,2); ?>
    </b>
    </div>
    <button class="btn-print" onclick="window.print()">
        🖨 Print Report
    </button>
    <div style="clear:both;"></div>
    </div>
    </div>
    <?php
    }
    ?>
</body>
</html>