<?php
session_start();
include 'db.php';

// Ensure that the CSRF token is generated and present in the session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a new CSRF token
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Token Validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    // Sanitize and validate inputs
    $fname = htmlspecialchars(trim($_POST['fName']), ENT_QUOTES, 'UTF-8');
    $lname = htmlspecialchars(trim($_POST['lName']), ENT_QUOTES, 'UTF-8');
    $phoneNum = htmlspecialchars(trim($_POST['num']), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Check if any field is empty
    if (empty($fname) || empty($lname) || empty($phoneNum) || empty($email) || empty($password)) {
        echo "Please fill in all fields.";
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // Check if email already exists
    $checkEmailQuery = "SELECT * FROM site_client WHERE email = ?";
    $checkStmt = $conn->prepare($checkEmailQuery);
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo "Email is already registered. Please use a different email.";
        exit;
    }
    $checkStmt->close();

    // Password strength validation (basic check)
    if (strlen($password) < 8) {
        echo "Password must be at least 8 characters long.";
        exit;
    }

    // Hash the password using bcrypt
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert the user with the hashed password
    $sql = "INSERT INTO site_client (fname, lname, phoneNum, email, pwd) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $fname, $lname, $phoneNum, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "User registered successfully!";
        header("Location: login.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

