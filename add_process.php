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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);

    // Prepare SQL statement for insertion
    $sql = "INSERT INTO notes (user_id, title, description, created_at) VALUES (?, ?, ?, NOW())";
    
    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Retrieve the user_id from the session
    $user_id = $_SESSION['user_id'];

    // Bind the parameters
    $stmt->bind_param("iss", $user_id, $title, $description);

    // Execute the statement
    if ($stmt->execute()) {
        // Note added successfully, redirect to dashboard or any other desired page
        header("Location: dashboard.php");
        exit();
    } else {
        // Error occurred while adding note
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
