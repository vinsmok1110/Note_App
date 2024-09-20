<?php

// Function to connect to the database using environment variables
function connectDB() {
    try {
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_DATABASE');
        $username = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');

        // Use MySQLi to connect to the database
        $conn = new mysqli($host, $username, $password, $dbname);

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        return $conn;
    } catch (Exception $e) {
        echo "Connection failed: " . $e->getMessage();
        return null;
    }
}

// Establish database connection
$conn = connectDB();

// Check if 'notes_id' parameter is set and not empty
if (isset($_GET['notes_id']) && !empty($_GET['notes_id'])) {
    $notes_id = $_GET['notes_id'];

    // Check if the database connection is established
    if ($conn) {
        // Insert the note ID into the archive table
        $archive_sql = "INSERT INTO archive (notes_id) VALUES (?)";

        // Create a prepared statement
        $stmt = $conn->prepare($archive_sql);

        // Check if statement preparation succeeded
        if ($stmt) {
            // Bind parameters (single 'i' for an integer parameter)
            $stmt->bind_param("i", $notes_id);

            // Execute the statement
            if ($stmt->execute()) {
                // Note archived successfully
                header("Location: dashboard.php");
                exit();
            } else {
                // Error executing the statement
                echo "Error archiving note: " . $stmt->error;
            }
        } else {
            // Error preparing the statement
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        // Failed to establish a database connection
        echo "Failed to establish database connection.";
    }
} else {
    // Handle the case where 'notes_id' is not set or empty
    echo "Invalid request: 'notes_id' parameter is missing or empty.";
}
?>
