<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar">

    <div class="logo">
        <img src="images/logo.png" alt="PawCare Logo">
        <span>PawCare</span>
    </div>

    <ul class="nav-links">

        <li><a href="booking.php">Booking</a></li>

        <li><a class="active" href="ownerDetails.php">Owner Details</a></li>

        <li><a href="petDetails.php">Pet Details</a></li>

    </ul>

    <div class="profile">

        <span>
            Welcome,
            <?php
            echo isset($_SESSION['customerName']) ? $_SESSION['customerName'] : "User";
            ?>
        </span>

        <img src="images/profile.png">

    </div>

</nav>