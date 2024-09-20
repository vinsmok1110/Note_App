<?php
// Database connection
include_once 'includes/db_connect.php'; // Include your database connection file

// Check if 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $note_id = $_GET['id'];
    $fav_id = 1; // You can set the favorite ID here; it could be dynamic as well if needed

    // Prepare SQL query to insert the note into the 'favorite_note' table
    $favorite_sql = "INSERT INTO favorite_note (notes_id, fav_id) VALUES (?, ?)";

    // Create a prepared statement
    if ($stmt = $conn->prepare($favorite_sql)) {
        // Bind parameters (both are integers)
        $stmt->bind_param("ii", $note_id, $fav_id);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to dashboard or other desired page
            header("Location: dashboard.php");
            exit();
        } else {
            // Error executing the query
            echo "Error marking note as favorite: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        // Error preparing the SQL statement
        echo "Error preparing the query: " . $conn->error;
    }
} else {
    // Handle the case where 'id' parameter is missing
    echo "Invalid request: 'id' parameter is missing.";
}

// Close the database connection
$conn->close();
?>
