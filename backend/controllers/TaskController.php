<?php
session_start();
require_once '../config/db.php';
require_once '../models/Task.php';

$database = new Database();
$db = $database->getConnection();

$task = new Task($db);

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'create') {
            $task->title = $_POST['title'];
            $task->description = $_POST['description'];
            $task->category = $_POST['category'];
            $task->due_date = $_POST['due_date'];
            $task->status = $_POST['status'];
            $task->user_id = $_SESSION['user_id'];
            $task->create();
        } elseif ($_POST['action'] == 'update') {
            $task->id = $_POST['id'];
            $task->title = $_POST['title'];
            $task->description = $_POST['description'];
            $task->category = $_POST['category'];
            $task->due_date = $_POST['due_date'];
            $task->status = $_POST['status'];
            $task->update();
        } elseif ($_POST['action'] == 'delete') {
            $task->id = $_POST['id'];
            $task->delete();
        }
        header('Location: ../views/dashboard.php');
        exit();
    }
}
?>
