<?php

$conn = new mysqli('localhost', 'root', '', 'tumblrHub');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Create Operation: Add a new Tumblr to the database
if (isset($_POST['create'])) {
    $tumblrName = $_POST['tumblrName'];
    $tumblrDescription = $_POST['tumblrDescription'];
    $quantityAvailable = $_POST['quantityAvailable'];
    $price = $_POST['price'];
    $size = $_POST['size'];
    $productAddedBy = "Ipseeka Malla"; // Hardcoded or fetched from session/user input

    // Insert Tumblr into the database with ProductAddedBy
    $sql = "INSERT INTO tumblrs (TumblrName, TumblrDescription, QuantityAvailable, Price, Size, ProductAddedBy) 
            VALUES ('$tumblrName', '$tumblrDescription', $quantityAvailable, $price, '$size', '$productAddedBy')";
    
    if ($conn->query($sql) === TRUE) {
        $message = "New Tumblr added successfully by $productAddedBy!";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Update Operation: Update Tumblr details in the database
if (isset($_POST['update'])) {
    $tumblrID = $_POST['tumblrID'];
    $tumblrName = $_POST['tumblrName'];
    $tumblrDescription = $_POST['tumblrDescription'];
    $quantityAvailable = $_POST['quantityAvailable'];
    $price = $_POST['price'];
    $size = $_POST['size'];

    // Update Tumblr in the database
    $sql = "UPDATE tumblrs SET TumblrName='$tumblrName', TumblrDescription='$tumblrDescription', 
            QuantityAvailable=$quantityAvailable, Price=$price, Size='$size' WHERE TumblrID=$tumblrID";

    if ($conn->query($sql) === TRUE) {
        $message = "Tumblr updated successfully!";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Delete Operation: Delete a Tumblr from the database
if (isset($_POST['delete'])) {
    $tumblrID = $_POST['tumblrID'];

    // Delete Tumblr from the database
    $sql = "DELETE FROM tumblrs WHERE TumblrID=$tumblrID";

    if ($conn->query($sql) === TRUE) {
        $message = "Tumblr deleted successfully!";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Read Operation: Fetch all Tumblrs from the database
$sql = "SELECT * FROM tumblrs";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TumblrHub - Ecommerce Site</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">TumblrHub</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#availableTumblrs">Available Tumblrs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#addTumblr">Add Tumblr</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="hero">
        <h1>Welcome to Your Tumblr Hub</h1>
    </div>

    <div class="container mt-5">
        <h3>Hello Admin,</h3>
       
        <p class="text-success text-center"><?php echo $message; ?></p>

        <!-- READ AND DELETE SECTION (Available Tumblrs) -->
        <h2 id="availableTumblrs" class="text-center my-4">Discover Our Available Tumblrs</h2>
        <div class="list-group">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="list-group-item">';
                    echo '<h5 class="mb-1">' . $row["TumblrName"] . '</h5>';
                    echo '<p class="mb-1">' . $row["TumblrDescription"] . '</p>';
                    echo '<p><strong>Quantity:</strong> ' . $row["QuantityAvailable"] . '</p>';
                    echo '<p><strong>Price:</strong> $' . $row["Price"] . '</p>';
                    echo '<p><strong>Size:</strong> ' . $row["Size"] . '</p>';
                    echo '<p><strong>Added By:</strong> ' . $row["ProductAddedBy"] . '</p>';  // Display who added the Tumblr
                    echo '<div class="d-flex justify-content-between align-items-center">';
                    echo '<button class="btn btn-success btn-sm edit" onclick="populateForm(' . $row["TumblrID"] . ', \'' . addslashes($row["TumblrName"]) . '\', \'' . addslashes($row["TumblrDescription"]) . '\', ' . $row["QuantityAvailable"] . ', ' . $row["Price"] . ', \'' . addslashes($row["Size"]) . '\')"><a href="#addTumblr">Edit</a></button>';
                    echo '<form method="post" action="" style="display:inline;">
                            <input type="hidden" name="tumblrID" value="' . $row["TumblrID"] . '">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                          </form>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<div class='list-group-item'>No Tumblrs found</div>";
            }
            ?>
        </div>

        <hr>

        <div class="tumblr">
            <h2 id="addTumblr" class="text-center my-4">Add Tumblr</h2>
            <form method="post" action="">
                <input type="hidden" name="tumblrID" id="tumblrID">

                <div class="form-group">
                    <label for="tumblrName">Tumblr Name:</label>
                    <input type="text" name="tumblrName" id="tumblrName" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="tumblrDescription">Tumblr Description:</label>
                    <textarea name="tumblrDescription" id="tumblrDescription" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label for="quantityAvailable">Quantity Available:</label>
                    <input type="number" name="quantityAvailable" id="quantityAvailable" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="text" name="price" id="price" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="size">Size:</label>
                    <input type="text" name="size" id="size" class="form-control" required>
                </div>

                <button type="submit" name="create" class="btn btn-primary btn-block">Add Tumblr</button>
                <button type="submit" name="update" class="btn btn-warning btn-block">Update Tumblr</button>
            </form>
        </div>
    </div>

    <footer class="footer text-center">
        <div class="container">
            <p>&copy; 2024 TumblrHub. All Rights Reserved.</p>
            <p>Contact: <a href="mailto:info@tumblrhub.com">info@tumblrhub.com</a></p>
            <p>Follow us on 
                <a href="#" target="_blank">Facebook</a>, 
                <a href="#" target="_blank">Twitter</a>, 
                <a href="#" target="_blank">Instagram</a>
            </p>
        </div>
    </footer>

    <script>
        // Function to populate the form for editing
        function populateForm(tumblrID, tumblrName, tumblrDescription, quantityAvailable, price, size) {
            document.getElementById('tumblrID').value = tumblrID;
            document.getElementById('tumblrName').value = tumblrName;
            document.getElementById('tumblrDescription').value = tumblrDescription;
            document.getElementById('quantityAvailable').value = quantityAvailable;
            document.getElementById('price').value = price;
            document.getElementById('size').value = size;
        }
    </script>
</body>
</html>
