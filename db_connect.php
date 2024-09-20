<?php
// Database connection using environment variables for Coolify deployment
$conn = mysqli_connect(
    getenv('DB_HOST'),       // Database host (from Coolify)
    getenv('DB_USERNAME'),   // Database username (from Coolify)
    getenv('DB_PASSWORD'),   // Database password (from Coolify)
    getenv('DB_DATABASE'),   // Database name (from Coolify)
    getenv('DB_PORT') ?: '3306' // Database port, default to 3306 if not set
);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
