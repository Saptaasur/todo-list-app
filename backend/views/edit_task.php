<?php
session_start();
require_once '../config/db.php';

$database = new Database();
$db = $database->getConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$task_id = $_GET['id'] ?? null;
$task = null;

if ($task_id) {
    $stmt = $db->prepare("SELECT * FROM tasks WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $task_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        die('Task not found or you do not have permission to edit it.');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    // Retrieve form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    // Update task in the database
    $stmt = $db->prepare("UPDATE tasks SET title = :title, description = :description, category = :category, due_date = :due_date, status = :status WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':due_date', $due_date);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $task_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();

    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f6f7;
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: 2rem auto;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        h2 {
            margin-top: 0;
            font-size: 24px;
            color: #333;
        }
        form {
            margin-top: 1rem;
        }
        label {
            font-weight: bold;
            margin-bottom: 0.5rem;
            display: block;
            color: #333;
        }
        input[type="text"], input[type="date"], textarea, select {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 1rem;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        /* Responsive styles */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
                margin: 1rem;
            }
        }

        @media (max-width: 480px) {
            input[type="text"], input[type="date"], textarea, select {
                padding: 8px;
            }
            button {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Task</h2>
        <form action="" method="POST">
            <label for="title">Task Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
            
            <label for="description">Task Description:</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($task['description']); ?></textarea>
            
            <label for="category">Category:</label>
            <select id="category" name="category">
                <option value="Work" <?php echo $task['category'] == 'Work' ? 'selected' : ''; ?>>Work</option>
                <option value="Personal" <?php echo $task['category'] == 'Personal' ? 'selected' : ''; ?>>Personal</option>
            </select>
            
            <label for="due_date">Due Date:</label>
            <input type="date" id="due_date" name="due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>">
            
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="Pending" <?php echo $task['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="In Progress" <?php echo $task['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="Completed" <?php echo $task['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
            </select>
            
            <input type="hidden" name="action" value="update">
            <button type="submit">Update Task</button>
        </form>
    </div>
</body>
</html>
