<?php
// Check which button was clicked and redirect to the appropriate page
if (isset($_POST['signin'])) {
    header("Location: signin.php"); // Redirect to signin page
    exit();
} elseif (isset($_POST['signup'])) {
    header("Location: signin.php"); // Redirect to signup page
    exit();
}
?>  