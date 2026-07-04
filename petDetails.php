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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="background">
    <div class="card">

    <h2>🐾 Pet Details</h2>
    <?php
        if(isset($_SESSION['success'])){
    ?>

    <div class="success-message">
        <?php
        echo $_SESSION['success'];
        unset($_SESSION['success']);
        ?>
    </div>

    <?php
        }
            if(isset($_SESSION['error']))
        {
    ?>

    <div class="error-message">
        <?php
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        ?>
    </div>
    <?php
        }
    ?>

    <form action="<?php echo ($edit) ? 'updatePet.php' : 'savePet.php'; ?>" method="POST">
        <hr><h2>🐶 My Pets</h2>
        <input type="text" id="searchPet" placeholder="Search pet..."
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Breed</th>
                <th>Birth Date</th>
                <th>Action</th>
            </tr>
            <?php
                $sql = "SELECT * FROM pet
                WHERE customerID='$customerID'
                ORDER BY petName";
                $result = mysqli_query($conn,$sql);
                while($row=mysqli_fetch_assoc($result))
                {
            ?>
            <tr>
                <td><?php echo $row['petID']; ?></td>
                <td><?php echo $row['petName']; ?></td>
                <td><?php echo $row['petType']; ?></td>
                <td><?php echo $row['breed']; ?></td>
                <td><?php echo $row['birthDate']; ?></td>
                <td>
                    <a
                        href="deletePet.php?id=<?php echo $row['petID']; ?>"
                        onclick="return confirm('Are you sure you want to delete <?php echo $row['petName']; ?>?');"
                        class="delete-btn">
                        🗑 Delete
                    </a>
                </td>
            </tr>
            <?php } ?>
        </table>
        <input type="hidden" name="customerID" value="<?php echo $customerID; ?>">
        <input type="hidden" name="petID" value="<?php echo $petID; ?>">

        <div class="form-group">
            <label>Pet Name</label>
            <input type="text" name="petName" value="<?php echo $petName; ?>" required>
        </div>

        <div class="form-group">
            <label>Pet Type</label>
            <select name="petType" required>
                <option value="">-- Select Pet Type --</option>
                <option value="Dog" <?php if($petType=="Dog") echo "selected"; ?>> Dog </option>
                <option value="Cat" <?php if($petType=="Cat") echo "selected"; ?>> Cat </option>
                <option value="Rabbit" <?php if($petType=="Rabbit") echo "selected"; ?>> Rabbit </option>
                <option value="Bird" <?php if($petType=="Bird") echo "selected"; ?>> Bird </option>                </select>
        </div>

        <div class="form-group">
            <label>Breed</label>
            <input type="text" name="breed" value="<?php echo $breed; ?>" required>
        </div>

        <div class="form-group">
            <label>Birth Date</label>
            <input type="date" name="birthDate" value="<?php echo $birthDate; ?>" required>
        </div>

        <div class="buttons">
            <button type="submit" name="<?php echo ($edit) ? 'update' : 'save'; ?>">
                <?php if($edit) echo "Update Pet"; else echo "Add Pet"; ?>
            </button>
            <?php if($edit){ ?>

            <a href="petDetails.php">
                <button type="button"> Cancel </button>
            </a>

            <?php } ?>
        </div>
    </form>
    </div>
    </div>
</body>
</html>