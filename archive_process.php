<?php
// Start PHP block
session_start(); // Start session (if not already started)

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or display an error message
    header("Location: login.php"); // Replace login.php with your actual login page
    exit();
}

// Establish a database connection using environment variables
$conn = mysqli_connect(
    getenv('DB_HOST'),        // Database host (e.g., localhost or Coolify server)
    getenv('DB_USERNAME'),    // Database username from environment
    getenv('DB_PASSWORD'),    // Database password from environment
    getenv('DB_DATABASE'),    // Database name from environment
    getenv('DB_PORT') ?: '3306' // Database port, default to 3306 if not set
);

// Check if the connection was successful
if (!$conn) {
    // Output the connection error and stop the script
    die("Connection failed: " . mysqli_connect_error());
}

// Logged-in user's ID
$user_id = $_SESSION['user_id'];

// SQL query to retrieve archived notes of the logged-in user with title, content, and created_at
$sql = "SELECT notes.notes_id, notes.title, notes.description, notes.created_at 
        FROM archive 
        INNER JOIN notes ON archive.notes_id = notes.notes_id 
        WHERE notes.user_id = ?";

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind the user ID as an integer

// Execute query
$stmt->execute();
$result = $stmt->get_result();

// Initialize $notes array to store fetched data
$notes = [];

// Loop through the retrieved notes and store them in $notes array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notes[] = $row;
    }
}

// Close the prepared statement and the database connection
$stmt->close();
$conn->close();
// End PHP block
?>
