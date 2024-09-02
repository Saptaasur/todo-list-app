<?php
session_start();
require_once '../config/db.php';
require_once '../models/Task.php';

$database = new Database();
$db = $database->getConnection();

$task = new Task($db);
$task->user_id = $_SESSION['user_id'];
$tasks = $task->readAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task List</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Your Tasks</h1>
    <a href="create_task.php">Create New Task</a>
    <ul>
        <?php foreach ($tasks as $task): ?>
            <li>
                <?php echo htmlspecialchars($task['title']); ?>
                <a href="edit_task.php?id=<?php echo htmlspecialchars($task['id']); ?>">Edit</a>
                <a href="delete_task.php?id=<?php echo htmlspecialchars($task['id']); ?>">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
