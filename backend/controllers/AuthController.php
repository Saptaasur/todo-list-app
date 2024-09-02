<?php
session_start();
require_once '../config/db.php';
require_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User($db);
    if (isset($_POST['action']) && $_POST['action'] == 'register') {
        $user->username = $_POST['username'];
        $user->email = $_POST['email'];
        $user->password = $_POST['password'];
        if ($user->register()) {
            $_SESSION['user_id'] = $user->id;
            header("Location: dashboard.php");
        } else {
            echo "Registration failed.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'login') {
        $user->email = $_POST['email'];
        $user->password = $_POST['password'];
        if ($user->login()) {
            $_SESSION['user_id'] = $user->id;
            header("Location: dashboard.php");
        } else {
            echo "Login failed.";
        }
    }
}
?>
