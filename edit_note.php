<?php
// Start the session to access user_id
session_start();

// Establish a database connection using environment variables
$conn = new mysqli(
    getenv('DB_HOST'),        // Database host
    getenv('DB_USERNAME'),    // Database username
    getenv('DB_PASSWORD'),    // Database password
    getenv('DB_DATABASE'),    // Database name
    getenv('DB_PORT') ?: '3306' // Database port, default to 3306 if not set
);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in and get their user ID
if (!isset($_SESSION['user_id'])) {
    // Redirect user to login page or handle unauthorized access
    header("Location: index.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Check if the form is submitted for editing a note
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title']) && isset($_POST['description']) && isset($_GET['notes_id'])) {
    $notes_id = $_GET['notes_id'];
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    
    // Update note in the database
    $sql = "UPDATE notes SET title = ?, description = ? WHERE notes_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters for the query
    $stmt->bind_param("ssii", $title, $description, $notes_id, $user_id);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back to the dashboard or wherever you want
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error updating note: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    // If the form is not submitted properly, redirect back to the dashboard
    header("Location: dashboard.php");
    exit();
}

// Close the connection
$conn->close();
?>
