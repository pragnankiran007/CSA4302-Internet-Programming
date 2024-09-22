<?php
// Start session to store login status and messages
session_start();

// Database connection details
$servername = "localhost"; // Modify as needed
$username = "root"; // Database username
$password = ""; // Database password
$dbname = "pro"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize and validate user input
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $username = sanitize_input($_POST['username']);
    $password = sanitize_input($_POST['password']);

    // Check if fields are empty
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Both username and password are required.";
        header("Location: login.html");
        exit();
    }

    // Retrieve user data from the database
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Login successful, store user information in the session
            $_SESSION['username'] = $user['username'];
            $_SESSION['login'] = true;

            // Redirect to a welcome page or user dashboard
            header("Location: home.html");
            exit();
        } else {
            // Incorrect password
            $_SESSION['error'] = "Incorrect password.";
            header("Location: login.html");
            exit();
        }
    } else {
        // User not found
        $_SESSION['error'] = "User not found.";
        header("Location: login.html");
        exit();
    }

    // Close the prepared statement and connection
    $stmt->close();
    $conn->close();
} else {
    header("Location: login.html");
    exit();
}
?>
