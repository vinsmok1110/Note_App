<?php
// Include database connection
include 'db_connect.php';

// Start session
session_start();

// Check if the login form has been submitted
if (!isset($_POST['username'], $_POST['password'])) {
    exit('Please complete the login form!');
}

$errors = array(); // Array to hold validation errors

// Validate input
if (empty($_POST['username'])) {
    $errors['username'] = 'Please enter a username';
}

if (empty($_POST['password'])) {
    $errors['password'] = 'Please enter a password';
}

// If there are any errors, display them
if (!empty($errors)) {
    echo json_encode($errors); // Output errors as JSON for JavaScript to handle
    exit(); // Stop further execution of the script
}

// Prepare the SQL statement to retrieve the account details
if ($stmt = $con->prepare('SELECT user_id, password FROM accounts WHERE username = ?')) {
    // Bind parameters (s = string)
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();

    // Check if the account exists
    if ($stmt->num_rows > 0) {
        // Bind the result to variables
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();
        
        // Verify the password
        if (password_verify($_POST['password'], $hashed_password)) {
            // Success: The password is correct, start the session
            session_start();
            // Store the user ID and username in session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $_POST['username'];

            // Redirect to the dashboard
            header('Location: dashboard.php');
            exit(); // Ensure no further code is executed after redirection
        } else {
            // Incorrect password
            $errors['password'] = 'Incorrect password';
            echo json_encode($errors); // Output errors as JSON for JavaScript to handle
        }
    } else {
        // Username doesn't exist
        $errors['username'] = 'Username does not exist';
        echo json_encode($errors); // Output errors as JSON for JavaScript to handle
    }
    $stmt->close();
} else {
    // Something is wrong with the SQL statement
    $errors['general'] = 'Could not prepare statement';
    echo json_encode($errors); // Output errors as JSON for JavaScript to handle
    exit(); // Stop further execution of the script
}
$con->close();
?>
