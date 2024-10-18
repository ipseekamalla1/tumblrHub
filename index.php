<?php
session_start(); // Start the session for user login

// Database connection
function dbConnect() {
    $conn = new mysqli('localhost', 'root', '', 'tumblrHub');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Check if the user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['username']);
}

// Handle login
function login($username, $password) {
    $conn = dbConnect();
    $sql = "SELECT * FROM users WHERE Username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Compare the entered password with the plain text password in the database
        if ($password === $user['Password']) {
            $_SESSION['username'] = $username; // Store username in session
            return "Login successful!";
        } else {
            return "Incorrect password.";
        }
    } else {
        return "Username not found.";
    }
}

// Logout function
function logout() {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Function to create a new Tumblr (Restricted to logged-in users)
function addTumblr($tumblrName, $tumblrDescription, $quantityAvailable, $price, $size, $material, $color, $productAddedBy) {
    if (!isUserLoggedIn()) {
        return "Please login to add a Tumblr.";
    }

    $conn = dbConnect();
    $sql = "INSERT INTO tumblrs (TumblrName, TumblrDescription, QuantityAvailable, Price, Size, Material, Color, ProductAddedBy) 
            VALUES ('$tumblrName', '$tumblrDescription', $quantityAvailable, $price, '$size', '$material', '$color', '$productAddedBy')";
    if ($conn->query($sql) === TRUE) {
        return "New Tumblr added successfully by $productAddedBy!";
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Function to update an existing Tumblr
function updateTumblr($tumblrID, $tumblrName, $tumblrDescription, $quantityAvailable, $price, $size, $material, $color) {
    $conn = dbConnect();
    $sql = "UPDATE tumblrs SET TumblrName='$tumblrName', TumblrDescription='$tumblrDescription', 
            QuantityAvailable=$quantityAvailable, Price=$price, Size='$size', Material='$material', Color='$color' 
            WHERE TumblrID=$tumblrID";
    if ($conn->query($sql) === TRUE) {
        return "Tumblr updated successfully!";
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Function to delete a Tumblr
function deleteTumblr($tumblrID) {
    $conn = dbConnect();
    $sql = "DELETE FROM tumblrs WHERE TumblrID=$tumblrID";
    if ($conn->query($sql) === TRUE) {
        return "Tumblr deleted successfully!";
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Function to fetch all tumblrs
function fetchTumblrs() {
    $conn = dbConnect();
    $sql = "SELECT * FROM tumblrs";
    $result = $conn->query($sql);
    return $result;
}

// Initialize message variables
$message = "";
$loginMessage = "";
$productAddedBy = isUserLoggedIn() ? $_SESSION['username'] : "Unknown";

// Handle form submissions for login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['login'])) {
        $loginMessage = login($_POST['username'], $_POST['password']);
    } elseif (isset($_POST['logout'])) {
        logout();
    } elseif (isset($_POST['create'])) {
        $message = addTumblr($_POST['tumblrName'], $_POST['tumblrDescription'], $_POST['quantityAvailable'], $_POST['price'], $_POST['size'], $_POST['material'], $_POST['color'], $productAddedBy);
    } elseif (isset($_POST['update'])) {
        $message = updateTumblr($_POST['tumblrID'], $_POST['tumblrName'], $_POST['tumblrDescription'], $_POST['quantityAvailable'], $_POST['price'], $_POST['size'], $_POST['material'], $_POST['color']);
    } elseif (isset($_POST['delete'])) {
        $message = deleteTumblr($_POST['tumblrID']);
    }
}

// Fetch all tumblrs for display
$tumblrs = fetchTumblrs();
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
            <?php if (!isUserLoggedIn()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="#login">Login</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <form method="post" action="">
                        <button type="submit" name="logout" class="btn btn-danger">Logout</button>
                    </form>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="hero">
    <h1>Welcome to Your Tumblr Hub</h1>
</div>

<div class="container mt-5">
    <h3>Hello Admin,</h3>
    <p class="text-success text-center"><?php echo $message; ?></p>
    
    <!-- Login Section -->
    <?php if (!isUserLoggedIn()): ?>
        <div class="login-section">
            <h2 id="login" class="text-center my-4">Login</h2>
            <p class="text-danger text-center"><?php echo $loginMessage; ?></p>
            <form method="post" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- READ AND DELETE SECTION (Available Tumblrs) -->
    <h2 id="availableTumblrs" class="text-center my-4">Discover Our Available Tumblrs</h2>
    <div class="list-group">
        <?php
        if ($tumblrs->num_rows > 0) {
            while($row = $tumblrs->fetch_assoc()) {
                echo '<div class="list-group-item">';
                echo '<h5 class="mb-1">' . $row["TumblrName"] . '</h5>';
                echo '<p class="mb-1">' . $row["TumblrDescription"] . '</p>';
                echo '<p><strong>Quantity:</strong> ' . $row["QuantityAvailable"] . '</p>';
                echo '<p><strong>Price:</strong> $' . $row["Price"] . '</p>';
                echo '<p><strong>Size:</strong> ' . $row["Size"] . '</p>';
                echo '<p><strong>Material:</strong> ' . $row["Material"] . '</p>';
                echo '<p><strong>Color:</strong> ' . $row["Color"] . '</p>';
                echo '<p><strong>Added By:</strong> ' . $row["ProductAddedBy"] . '</p>';

                // Edit Button: Populate the form with existing Tumblr data
                echo '<form method="post" action="" style="display:inline;">
                        <input type="hidden" name="tumblrID" value="' . $row["TumblrID"] . '">
                        <button type="submit" name="update" class="btn btn-warning btn-sm">Edit</button>
                      </form>';

                // Delete Button
                echo '<form method="post" action="" style="display:inline;">
                        <input type="hidden" name="tumblrID" value="' . $row["TumblrID"] . '">
                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                      </form>';
                echo '</div>';
            }
        } else {
            echo "<div class='list-group-item'>No Tumblrs found</div>";
        }
        ?>
    </div>

    <hr>

    <!-- Add Tumblr Section (Restricted to logged-in users) -->
    <?php if (isUserLoggedIn()): ?>
        <div class="tumblr">
            <h2 id="addTumblr" class="text-center my-4">Add or Edit Tumblr</h2>
            <form method="post" action="">
                <input type="hidden" name="tumblrID" id="tumblrID"> <!-- Hidden field for Tumblr ID -->
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

                <div class="form-group">
                    <label for="material">Material:</label>
                    <select name="material" id="material" class="form-control" required>
                        <option value="" disabled selected>Select Material</option>
                        <option value="Plastic">Plastic</option>
                        <option value="Glass">Glass</option>
                        <option value="Ceramic">Ceramic</option>
                        <option value="Metal">Metal</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="color">Color:</label>
                    <select name="color" id="color" class="form-control" required>
                        <option value="" disabled selected>Select Color</option>
                        <option value="Red">Red</option>
                        <option value="Blue">Blue</option>
                        <option value="Green">Green</option>
                        <option value="Yellow">Yellow</option>
                        <option value="Black">Black</option>
                        <option value="White">White</option>
                    </select>
                </div>

                <button type="submit" name="create" class="btn btn-primary btn-block">Add/Update Tumblr</button>
            </form>
        </div>
    <?php endif; ?>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
