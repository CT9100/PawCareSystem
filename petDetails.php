<?php
    session_start();
    include("connection.php");

    $customerID = $_SESSION['customerID'];
    $edit = false;
    $petID = "";
    $petName = "";
    $petType = "";
    $breed = "";
    $birthDate = "";

    if(isset($_GET['edit']))
    {
        $edit = true;
        $petID = $_GET['edit'];
        $sql = "SELECT * FROM pet WHERE petID='$petID'";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($result);
        $petName = $row['petName'];
        $petType = $row['petType'];
        $breed = $row['breed'];
        $birthDate = $row['birthDate'];
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pet Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    *{
        box-sizing:border-box;
        font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body{
        margin:0;
        background:#f4f7f6;
    }

    /* NAVBAR */
    .navbar{
        background:#8cd3e6;
        padding:15px 30px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        color:#2b5c8f;
    }

    /* BACKGROUND */
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

    /* CONTAINER */
    .container{
        max-width:1100px;
        margin:40px auto;
        padding:20px;
    }

    /* GLASS CARD */
    .card{
        background:rgba(255,255,255,0.92);
        backdrop-filter:blur(10px);
        border-radius:15px;
        padding:25px;
        box-shadow:0 10px 25px rgba(0,0,0,0.25);
        margin-bottom:25px;
    }

    /* GRID FORM */
    .form-grid{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:15px;
    }

    .form-group{
        display:flex;
        flex-direction:column;
    }

    .form-group label{
        font-size:12px;
        color:#777;
        margin-bottom:5px;
    }

    input, select{
        padding:12px;
        border-radius:10px;
        border:2px solid #eee;
    }

    /* BUTTON */
    button{
        padding:12px 18px;
        border:none;
        border-radius:20px;
        cursor:pointer;
        font-weight:bold;
    }

    button[name="save"], button[name="update"]{
        background:#8cd3e6;
        color:#2b5c8f;
    }

    button[type="button"]{
        background:#eee;
    }

    /* PET GRID */
    .pet-grid{
        display:grid;
        grid-template-columns:repeat(auto-fit, minmax(230px, 1fr));
        gap:20px;
    }

    /* PET CARD */
    .pet-card{
        background:rgba(255,255,255,0.92);
        border-radius:15px;
        padding:20px;
        box-shadow:0 10px 25px rgba(0,0,0,0.2);
        transition:0.3s;
    }

    .pet-card:hover{
        transform:translateY(-5px);
    }

    .pet-title{
        font-size:18px;
        font-weight:bold;
        color:#2b5c8f;
    }

    .pet-type{
        color:#777;
        margin-bottom:10px;
    }

    .actions a{
        text-decoration:none;
        font-size:13px;
        margin-right:10px;
    }

    .delete{
        color:red;
    }
    .edit{
        color:#f3be6b;
    }

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

    .layout{
        display:flex;
        gap:20px;
    }

    /* LEFT SIDE */
    .list-card{
        flex:2;
        max-height:80vh;
        overflow-y:auto;
    }

    /* RIGHT SIDE */
    .form-card{
        flex:1;
        display:none;
        flex-direction: column;
    }

     /* Top Navigation Header Matching image_47a522.png Layout Exactly */
        .navbar {
            background-color: #8cd3e6;
            padding: 10px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .logo-container {
            background: white;
            padding: 5px 10px;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            padding-bottom: 5px;
        }
        .nav-links a.active {
            border-bottom: 3px solid white;
        }
        
        .avatar-icon {
            width: 35px;
            height: 35px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
            font-weight: bold;
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

    .modal{
        display:none;
        position:fixed;
        top:0;
        left:0;
        width:100%;
        height:100%;
        background:rgba(0,0,0,0.6);
        z-index:10000;
        justify-content:center;
        align-items:center;
    }

    .modal-content{
        background:white;
        padding:30px;
        border-radius:15px;
        width:300px;
        text-align:center;
    }

    .close{
        float:right;
        cursor:pointer;
        font-size:22px;
    }
</style>
<body>
<div class="navbar">
        <div class="logo-container">
            <span style="color:#2b5c8f; font-weight:bold; font-size:18px;">🐾 PawCare</span>
        </div>
        <div class="nav-links">
            <a href="booking.php">Booking</a>
            <a href="petDetails.php"  class = "active">My Pets</a>
            <a href="ownerDetails.php">My Profile</a>
        </div>
    <div>
        <button onclick="toggleForm()" class="nav-btn">➕ Add Pet</button>
        <a href="dashboard.php" class="nav-btn">← Back</a>
    </div>
</div>
<video autoplay muted loop playsinline id="bg-video">
    <source src="videos/dog.mp4" type="video/mp4">
</video>

<div class="overlay"></div>

<div class="container">

    <div class="layout">

        <!-- LEFT: PET LIST -->
        <div class="card list-card">

            <h2>🐶 My Pets</h2>

            <div class="pet-grid">

            <?php
            $sql = "SELECT * FROM pet WHERE customerID='$customerID' ORDER BY petName";
            $result = mysqli_query($conn,$sql);

            while($row=mysqli_fetch_assoc($result)){
            ?>

                <div class="pet-card"
                    onclick="openPet(
                        '<?php echo addslashes($row['petName']); ?>',
                        '<?php echo addslashes($row['petType']); ?>',
                        '<?php echo addslashes($row['breed']); ?>',
                        '<?php echo $row['birthDate']; ?>'
                    )">

                    <div class="pet-title">
                        <?php echo $row['petName']; ?>
                    </div>

                    <div class="pet-type">
                        <?php echo $row['petType']; ?> • <?php echo $row['breed']; ?>
                    </div>

                    <div class="actions">
                        <a class="edit" href="petDetails.php?edit=<?php echo $row['petID']; ?>">Edit</a>

                        <a class="delete"
                           onclick="return confirm('Delete <?php echo $row['petName']; ?>?');"
                           href="deletePet.php?id=<?php echo $row['petID']; ?>">
                           Delete
                        </a>
                    </div>

                </div>

            <?php } ?>

            </div>

        </div>

        <!-- RIGHT: FORM (HIDDEN INITIALLY) -->
        <div class="card form-card" id="formCard">

            <h2>🐾 <?php echo $edit ? "Edit Pet" : "Add Pet"; ?></h2>

            <form action="<?php echo ($edit) ? 'updatePet.php' : 'savePet.php'; ?>" method="POST">

                <input type="hidden" name="customerID" value="<?php echo $customerID; ?>">
                <input type="hidden" name="petID" value="<?php echo $petID; ?>">

                <div class="form-group">
                    <label>Pet Name</label>
                    <input type="text" name="petName" value="<?php echo $petName; ?>" required>
                </div>

                <div class="form-group">
                    <label>Pet Type</label>
                    <select name="petType" required>
                        <option value="">Select</option>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        <option value="Rabbit">Rabbit</option>
                        <option value="Bird">Bird</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Breed</label>
                    <input type="text" name="breed" value="<?php echo $breed; ?>" required>
                </div>

                <div class="form-group">
                    <label>Birth Date</label>
                    <input type="date" name="birthDate" value="<?php echo $birthDate; ?>" required>
                </div>

                <button type="submit" name="<?php echo $edit ? 'update' : 'save'; ?>">
                    <?php echo $edit ? "Update Pet" : "Save Pet"; ?>
                </button>
            </form>
        </div>
    </div>
</div>
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
    window.onload = function(){

    document.querySelectorAll('.paw').forEach(paw=>{

        paw.style.left = Math.random()*100 + "vw";
        paw.style.animationDuration = (5 + Math.random()*6) + "s";
        paw.style.fontSize = (16 + Math.random()*22) + "px";
        paw.style.animationDelay = Math.random()*5 + "s";

    });

    <?php if($edit){ ?>
        document.getElementById("formCard").style.display="flex";
    <?php } ?>
};
</script>
<div id="petModal" class="modal">
    <div class="modal-content">

        <span class="close" onclick="closeModal()">&times;</span>

        <h2 id="mName"></h2>
        <p id="mType"></p>
        <p id="mBreed"></p>
        <p id="mBirth"></p>

    </div>
</div>
<script>
    function openPet(name,type,breed,birth){

        let birthDate = new Date(birth);
        let today = new Date();

        let age = today.getFullYear() - birthDate.getFullYear();
        let m = today.getMonth() - birthDate.getMonth();

        if(m < 0 || (m === 0 && today.getDate() < birthDate.getDate())){
            age--;
        }

        document.getElementById("petModal").style.display="flex";

        document.getElementById("mName").innerText = "🐾 " + name;
        document.getElementById("mType").innerText = "Type: " + type;
        document.getElementById("mBreed").innerText = "Breed: " + breed;
        document.getElementById("mBirth").innerText =
            "Birth: " + birth + " | Age: " + age + " years";
    }
</script>
<script>
    function closeModal(){
    let modal = document.getElementById("petModal");
    if(modal){
        modal.style.display = "none";
    }
}
</script>
<script>
function toggleForm(){
    const form = document.getElementById("formCard");

    if(!form) return;

    if(form.style.display === "flex"){
        form.style.display = "none";
    } else {
        form.style.display = "flex";
        form.scrollIntoView({behavior:"smooth"});
    }
}
</script>
</body>
</html>