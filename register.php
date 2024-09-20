<?php
// Include the database connection
include 'db_connect.php'; // Ensure this contains the $conn variable

// Start the registration process

// Check if the data was submitted
if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
    // Could not get the data that should have been sent
    exit('Please complete the registration form!');
}

$errors = array(); // Array to hold validation errors

// Validate input fields
if (empty($_POST['username'])) {
    $errors['username'] = 'Please enter a username';
}

if (empty($_POST['password'])) {
    $errors['password'] = 'Please enter a password';
}

if (empty($_POST['email'])) {
    $errors['email'] = 'Please enter an email';
}

// Check if passwords match
if ($_POST['password'] !== $_POST['confirmpass']) {
    $errors['confirmpass'] = 'Passwords do not match';
}

// If there are any errors, return them as JSON
if (!empty($errors)) {
    echo json_encode($errors);
    exit(); // Stop the script if there are errors
}

// Check if the account with that username exists
if ($stmt = $conn->prepare('SELECT user_id FROM accounts WHERE username = ?')) { // Changed $con to $conn
    // Bind the username parameter
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();

    // Check if username already exists
    if ($stmt->num_rows > 0) {
        $errors['username'] = 'Username already exists, please choose another';
        echo json_encode($errors);
        exit(); // Stop further execution of the script
    } else {
        // Insert new account into the database
        if ($stmt = $conn->prepare('INSERT INTO accounts (username, password, email) VALUES (?, ?, ?)')) { // Changed $con to $conn
            // Hash the password before storing it in the database
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt->bind_param('sss', $_POST['username'], $password, $_POST['email']);
            $stmt->execute();

            // Registration success message
            echo "<script>alert('Registration successful!')</script>";
            echo "<script>window.location.href = '../notefor/index.php';</script>";
        } else {
            // Handle SQL preparation error
            $errors['general'] = 'Could not prepare statement';
            echo json_encode($errors);
            exit(); // Stop further execution of the script
        }
    }
    $stmt->close();
} else {
    // Handle SQL preparation error
    $errors['general'] = 'Could not prepare statement';
    echo json_encode($errors);
    exit(); // Stop further execution of the script
}

$conn->close(); // Changed $con to $conn
?>
