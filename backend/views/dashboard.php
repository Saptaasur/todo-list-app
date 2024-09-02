<?php
include('../includes/header.php');

// Initialize error message
$error_message = '';

// Assuming you have a way to get the current user's ID.
session_start();
$current_user_id = $_SESSION['user_id'] ?? null;

if (!$current_user_id) {
    die('User not logged in.');
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'create') {
    // Handle task creation
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    try {
        $pdo = new PDO('mysql:host=localhost;dbname=todo_db', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, category, due_date, status, user_id) VALUES (:title, :description, :category, :due_date, :status, :user_id)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':user_id', $current_user_id);
        $stmt->execute();

        header('Location: dashboard.php');
        exit;
    } catch (PDOException $e) {
        $error_message = 'Database error: ' . $e->getMessage();
    }
}

// Fetch tasks from the database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=todo_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $current_user_id);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = 'Database error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Tasks</title>
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
        .logout-btn {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 1rem;
        }
        .logout-btn a {
            color: #ff4d4d;
            text-decoration: none;
            font-size: 18px;
            transition: color 0.3s ease;
        }
        .logout-btn a:hover {
            color: #cc0000;
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
        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        li {
            background-color: #ffffff;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        li h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        li p {
            margin: 0.5rem 0;
            color: #666;
        }
        li small {
            display: block;
            margin-top: 0.5rem;
            color: #888;
        }
        form {
            margin-bottom: 2rem;
        }
        .error-message {
            color: #ff4d4d;
            margin-bottom: 1rem;
        }
        .task-actions {
            display: flex;
            gap: 10px;
        }
        .task-actions a,
        .task-actions button {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
            font-size: 18px;
        }
        .task-actions a:hover,
        .task-actions button:hover {
            color: #0056b3;
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
        <div class="logout-btn">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <h2>Your Tasks</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="title">Task Title:</label>
            <input type="text" id="title" name="title" placeholder="Task Title" required>
            
            <label for="description">Task Description:</label>
            <textarea id="description" name="description" placeholder="Task Description"></textarea>
            
            <label for="category">Category:</label>
            <select id="category" name="category">
                <option value="Work">Work</option>
                <option value="Personal">Personal</option>
            </select>
            
            <label for="due_date">Due Date:</label>
            <input type="date" id="due_date" name="due_date">
            
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
            </select>
            
            <input type="hidden" name="action" value="create">
            <button type="submit">Add Task</button>
        </form>
        <ul>
            <?php foreach ($tasks as $task): ?>
                <li>
                    <div>
                        <h3><?php echo htmlspecialchars($task['title']); ?></h3>
                        <p><?php echo htmlspecialchars($task['description']); ?></p>
                        <small><?php echo htmlspecialchars($task['due_date']); ?> - <?php echo htmlspecialchars($task['status']); ?></small>
                    </div>
                    <div class="task-actions">
                        <a href="edit_task.php?id=<?php echo htmlspecialchars($task['id']); ?>"><i class="fas fa-edit"></i></a>
                        <form action="../controllers/TaskController.php" method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($task['id']); ?>">
                            <button type="submit" name="action" value="delete"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
