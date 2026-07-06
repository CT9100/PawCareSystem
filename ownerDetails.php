<?php
    session_start();
    include("connection.php");

    $customerID = $_SESSION['customerID'];
    $sql = "SELECT * FROM customer WHERE customerID='$customerID'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
<title>Owner Details</title>

<style>
    *{
        box-sizing:border-box;
        font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body{
        margin:0;
        background:#f4f7f6;
    }

    .navbar{
        background:#8cd3e6;
        padding:15px 30px;
        display:flex;
        justify-content:space-between;
        align-items:center;
    }

    .nav-btn{
        background:#f3be6b;
        padding:10px 15px;
        border-radius:20px;
        border:none;
        cursor:pointer;
        font-weight:bold;
        text-decoration:none;
        color:#333;
    }

    .container{
        max-width:900px;
        margin:50px auto;
        padding:0 20px;
    }

    .card h2{
        margin-top:0;
        color:#2b5c8f;
        margin-bottom:25px;
    }

    /* GRID LAYOUT */
    .form-grid{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:20px;
    }

    .form-group{
        display:flex;
        flex-direction:column;
    }

    .form-group label{
        font-size:13px;
        color:#777;
        margin-bottom:6px;
        text-transform:uppercase;
    }

    .editable{
        padding:12px;
        border:2px solid #eee;
        border-radius:10px;
        background:#f9f9f9;
        font-size:15px;
    }

    textarea.editable{
        resize:none;
        height:90px;
    }

    .full{
        grid-column:span 2;
    }

    /* BUTTONS */
    .buttons{
        margin-top:25px;
        display:flex;
        gap:10px;
    }

    button{
        padding:12px 20px;
        border:none;
        border-radius:20px;
        cursor:pointer;
        font-weight:bold;
    }

    #editBtn{
        background:#f3be6b;
        color:#333;
    }

    #saveBtn{
        background:#8cd3e6;
        color:#2b5c8f;
    }

    button[type="button"]:last-child{
        background:#eee;
    }

    button:hover{
        opacity:0.9;
    }

    #bg-video{
        position:fixed;
        top:0;
        left:0;
        width:100%;
        height:100%;
        object-fit:cover;
        z-index:-2;
    }

    .overlay{
        position:fixed;
        top:0;
        left:0;
        width:100%;
        height:100%;
        background:rgba(0,0,0,0.45);
        z-index:-1;
    }

    .card{
        background:rgba(255,255,255,0.92);
        backdrop-filter:blur(12px);
        padding:35px;
        border-radius:18px;
        box-shadow:0 15px 35px rgba(0,0,0,0.25);
        transition:0.3s;
        position:relative;
        overflow:hidden;
    }

    .card:hover{
        transform:translateY(-5px);
        box-shadow:0 20px 50px rgba(0,0,0,0.35);
    }

    /* subtle glowing border animation */
    .card::before{
        content:"";
        position:absolute;
        top:-2px;
        left:-2px;
        right:-2px;
        bottom:-2px;
        background:linear-gradient(45deg,#8cd3e6,#f3be6b,#8cd3e6);
        z-index:-1;
        border-radius:20px;
        opacity:0.4;
    }

    /* ===== AVATAR ===== */
    .avatar{
        width:80px;
        height:80px;
        border-radius:50%;
        background:linear-gradient(135deg,#8cd3e6,#f3be6b);
        display:flex;
        justify-content:center;
        align-items:center;
        font-size:30px;
        font-weight:bold;
        color:white;
        margin-bottom:10px;
        box-shadow:0 10px 20px rgba(0,0,0,0.2);
    }

    /* ===== FLOATING PAWS ===== */
    .paw{
        position:fixed;
        bottom:-50px;
        font-size:24px;
        opacity:0.35;
        z-index:1; /* IMPORTANT: above overlay */
        animation:floatUp linear infinite;
        pointer-events:none;
    }

    @keyframes floatUp{
        0%{
            transform:translateY(0) translateX(0) rotate(0deg);
            opacity:0;
        }
        10%{
            opacity:0.4;
        }
        100%{
            transform:translateY(-110vh) translateX(40px) rotate(360deg);
            opacity:0;
        }
    }
</style>
</head>

<body>
    <div class="overlay"></div>
    <video autoplay muted loop playsinline id="bg-video">
        <source src="videos/rabbit.mp4" type="video/mp4">
    </video>

<div class="navbar">
    <div>🐾 Owner Details</div>
    <a href="dashboard.php" class="nav-btn">← Back</a>
</div>

<div class="container">

<div class="card">
<h2>Owner Profile</h2>

<form action="updateOwner.php" method="POST">

    <input type="hidden" name="customerID" value="<?php echo $row['customerID']; ?>">

    <div class="form-grid">

        <div class="avatar">
            <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
        </div>

        <div class="form-group">
            <label>Name</label>
            <input class="editable" type="text" name="name" value="<?php echo $row['name']; ?>" readonly>
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input class="editable" type="text" name="phone" value="<?php echo $row['phone']; ?>" readonly>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input class="editable" type="email" name="email" value="<?php echo $row['email']; ?>" readonly>
        </div>

        <div class="form-group full">
            <label>Address</label>
            <textarea class="editable" name="address" readonly><?php echo $row['address']; ?></textarea>
        </div>

    </div>

    <div class="buttons">
        <button type="button" id="editBtn" onclick="enableEdit()">Edit</button>
        <button type="submit" name="save" id="saveBtn" style="display:none;">Save</button>
    </div>

</form>

</div>

</div>

<script>
function enableEdit(){
    let fields = document.querySelectorAll(".editable");

    fields.forEach(field => {
        field.removeAttribute("readonly");
        field.style.background = "white";
    });

    document.getElementById("editBtn").style.display = "none";
    document.getElementById("saveBtn").style.display = "inline-block";
}
</script>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <div class="paw">🐾</div>
    <script>
    document.querySelectorAll('.paw').forEach(paw => {

        // spread across full screen width
        paw.style.left = Math.random() * 100 + "vw";

        // random size (makes it cute, not robotic)
        let size = 18 + Math.random() * 22;
        paw.style.fontSize = size + "px";

        // different speeds (important for visibility)
        let duration = 6 + Math.random() * 6;
        paw.style.animationDuration = duration + "s";

        // delay so they don't spawn at same time
        paw.style.animationDelay = Math.random() * 5 + "s";
    });
    </script>
</body>
</html>