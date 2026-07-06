<?php
session_start();
include 'connection.php';

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
        
        .report-container { 
            padding: 50px; display: flex; justify-content: center; gap: 40px; 
            min-height: 60vh; position: relative; align-items: flex-start;
        }

        .data-box {
            background: rgba(184, 134, 11, 0.7); /* Transparent brown */
            padding: 30px; border-radius: 15px; width: 250px; text-align: center;
            min-height: 300px;
        }
        .data-box h3 { margin-top: 0; margin-bottom: 30px; }
        .data-line { border-bottom: 2px dashed white; margin: 20px 0; padding-bottom: 10px; font-size: 18px; }

        .chart-placeholder {
            font-size: 100px; color: white; align-self: center; margin-left: 50px;
        }

        .btn-print {
            position: absolute; bottom: 40px; right: 40px;
            background-color: #1ccbf2; color: white; border: none; 
            padding: 15px 30px; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer;
        }

        @media print {
            .navbar, .btn-print { display: none; }
            body { background: white; color: black; }
            .data-box { background: #f0f0f0; color: black; border: 1px solid #333; }
            .data-line { border-bottom: 2px dashed black; }
        }
        .btn-logout { color: red; text-decoration: none; margin: 0 15px; font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo">🐾 PawCare</div>
        <div class="nav-links">
            <a href="admin_dashboard.php">Booking record</a>
            <a href="time_slot.php">Time Slots</a>
            <a href="report.php" class="active">Report</a>
        </div>
        <div class="user-profile">
        <a href="logout.php" class="btn-logout" onclick="return confirm('Are you sure you want to log out?')">Logout</a>
        <div style="background: black; width: 35px; height: 35px; border-radius: 50%;"></div>
    </div>
    </div>

    <div class="report-container">
        
        <div class="data-box">
            <h3>Total Revenue</h3>
            <div class="data-line">RM <?php echo number_format($totalRevenue, 2); ?></div>
            <div class="data-line"></div>
            <div class="data-line"></div>
        </div>

        <div class="data-box">
            <h3>Customer list</h3>
            <?php 
            if ($customerResult && $customerResult->num_rows > 0) {
                while($row = $customerResult->fetch_assoc()) {
                    echo "<div class='data-line'>" . htmlspecialchars($row['name']) . "</div>";
                }
            } else {
                echo "<div class='data-line'>No customers found</div>";
            }
            ?>
        </div>

        <button class="btn-print" onclick="window.print()">Print to PDF</button>

    </div>

</body>
</html>