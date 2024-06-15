<?php
session_start();

$todoList = isset($_SESSION["todoList"]) ? $_SESSION["todoList"] : array();

function appendData($task, $date, $time) {
    return ['task' => $task, 'date' => $date, 'time' => $time];
}

function deleteData($toDelete, $todoList) {
    foreach ($todoList as $index => $taskData) {
        if ($taskData['task'] === $toDelete) {
            unset($todoList[$index]);
            break;
        }
    }
    return array_values($todoList);
}

function updateData($oldTask, $newTask, $newDate, $newTime, $todoList) {
    foreach ($todoList as $index => $taskData) {
        if ($taskData['task'] === $oldTask) {
            $todoList[$index] = ['task' => $newTask, 'date' => $newDate, 'time' => $newTime];
            break;
        }
    }
    return $todoList;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        if (empty($_POST["task"]) || empty($_POST["date"]) || empty($_POST["time"])) {
            echo '<script>alert("Error: All fields must be filled out")</script>';
        } else {
            $todoList = updateData($_POST["oldTask"], $_POST["task"], $_POST["date"], $_POST["time"], $todoList);
            $_SESSION["todoList"] = $todoList;
        }
    } else {
        if (empty($_POST["task"])) {
            echo '<script>alert("Error: Task cannot be empty")</script>';
        } else if (empty($_POST["date"])) {
            echo '<script>alert("Error: Date cannot be empty")</script>';
        } else if (empty($_POST["time"])) {
            echo '<script>alert("Error: Time cannot be empty")</script>';
        } else {
            $newTask = appendData($_POST["task"], $_POST["date"], $_POST["time"]);
            array_push($todoList, $newTask);
            $_SESSION["todoList"] = $todoList;
        }
    }
}

if (isset($_GET['task'])) {
    $todoList = deleteData($_GET['task'], $todoList);
    $_SESSION["todoList"] = $todoList;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Black Clover To-Do List</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('home.jpeg') no-repeat center center fixed;
            background-size: cover;
            color: #f8f8f2;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            background-color: rgba(46, 46, 46, 0.85);
            color: #f8f8f2;
            margin-bottom: 20px;
        }
        .task-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(61, 61, 61, 0.85);
        }
        .task-text {
            flex-grow: 1;
        }
        .btn-danger {
            background-color: #ff4444;
            border-color: #ff4444;
        }
        .btn-danger:hover {
            background-color: #ff7777;
            border-color: #ff7777;
        }
        .btn-primary {
            margin-right: 10px;
        }
        .back-button {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #3d9970;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #2e8b57;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Black Clover Themed To-Do List</h1>
        <div class="card">
            <div class="card-header">Add a New Task</div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="form-group">
                        <input type="text" class="form-control" name="task" placeholder="Enter your task here">
                    </div>
                    <div class="form-group">
                        <input type="date" class="form-control" name="date">
                    </div>
                    <div class="form-group">
                        <input type="time" class="form-control" name="time">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Task</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Tasks</div>
            <ul class="list-group list-group-flush">
                <?php if (empty($todoList)): ?>
                    <li class="list-group-item text-center" style="background-color: rgba(61, 61, 61, 0.85);">No tasks added yet.</li>
                <?php else: ?>
                    <?php foreach ($todoList as $taskData): ?>
                        <li class="list-group-item task-item">
                            <div class="task-text">
                                <strong><?php echo htmlspecialchars($taskData['task']); ?></strong><br>
                                <small>Date: <?php echo htmlspecialchars($taskData['date']); ?></small><br>
                                <small>Time: <?php echo htmlspecialchars($taskData['time']); ?></small>
                            </div>
                            <div>
                                <button class="btn btn-primary" onclick="editTask('<?php echo htmlspecialchars($taskData['task']); ?>', '<?php echo htmlspecialchars($taskData['date']); ?>', '<?php echo htmlspecialchars($taskData['time']); ?>')">Edit</button>
                                <a href="index.php?task=<?php echo urlencode($taskData['task']); ?>" class="btn btn-danger">Delete</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <div class="card" id="editForm" style="display:none;">
            <div class="card-header">Edit Task</div>
            <div class="card-body">
                <form method="post" action="">
                    <input type="hidden" name="oldTask" id="oldTask">
                    <div class="form-group">
                        <input type="text" class="form-control" name="task" id="editTask" placeholder="Enter your task here">
                    </div>
                    <div class="form-group">
                        <input type="date" class="form-control" name="date" id="editDate">
                    </div>
                    <div class="form-group">
                        <input type="time" class="form-control" name="time" id="editTime">
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">Update Task</button>
                </form>
            </div>
        </div>
    </div>

    <a href="index.html" class="back-button">Back to Home</a>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function editTask(task, date, time) {
            document.getElementById('editForm').style.display = 'block';
            document.getElementById('oldTask').value = task;
            document.getElementById('editTask').value = task;
            document.getElementById('editDate').value = date;
            document.getElementById('editTime').value = time;
        }
    </script>
</body>
</html>
