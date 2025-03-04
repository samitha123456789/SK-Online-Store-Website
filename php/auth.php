<?php
session_start(); // Start the session

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sk_online_store";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (!empty($username) && !empty($email) && !empty($password)) {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        
        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! You can now log in.'); window.location.href='../login.html';</script>";
        } else {
            echo "<script>alert('Error: Could not register. Email might already be registered.'); window.location.href='../login.html';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please fill in all fields.'); window.location.href='../login.html';</script>";
    }
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        // Check if admin credentials are entered
        if ($email === "admin@gmail.com" && $password === "admin") {
            $_SESSION['user'] = "admin";
            echo "<script>alert('Admin login successful!'); window.location.href='../admin.html';</script>";
            exit();
        }

        // Check if user exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['email'];
            echo "<script>alert('Login successful!'); window.location.href='../index.html';</script>";
        } else {
            echo "<script>alert('Invalid email or password.'); window.location.href='../login.html';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please fill in all fields.'); window.location.href='../login.html';</script>";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    echo "<script>window.location.href='../login.html';</script>";
    exit();
}

$conn->close();

?>
