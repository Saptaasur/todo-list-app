<?php
session_start();
require_once '../config/db.php';
require_once '../models/Task.php';

$database = new Database();
$db = $database->getConnection();

$task = new Task($db);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task->id = $_POST['id'];
    $task->delete();
    header("Location: task_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Task</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Delete Task</h1>
    <form id="delete-task-form" method="POST" action="delete_task.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
        <p>Are you sure you want to delete this task?</p>
        <button type="submit">Delete Task</button>
        <a href="task_list.php">Cancel</a>
    </form>
</body>
</html>
