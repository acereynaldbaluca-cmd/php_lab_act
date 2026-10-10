<?php

include "dbcon.php";

$message = "";
$messageType = "success";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if ($name === "" || $email === "") {
        $message = "Please fill in all fields.";
        $messageType = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "danger";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $email);

        if ($stmt->execute()) {
            $message = "User added successfully.";
        } else {
            $message = "Error adding user.";
            $messageType = "danger";
        }

        $stmt->close();
    }
}

if (isset($_GET["msg"])) {
    if ($_GET["msg"] == "updated") {
        $message = "User updated successfully.";
    } elseif ($_GET["msg"] == "deleted") {
        $message = "User deleted successfully.";
    }
}

$result = $conn->query("SELECT * FROM users ORDER BY id DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            background: #eef3f8;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 850px;
            max-width: 95%;
            margin: 80px auto;
            background: white;
            padding: 30px 35px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        h1 {
            text-align: center;
            font-size: 25px;
            color: #222;
            margin-top: 0;
            margin-bottom: 35px;
        }
        .content {
            display: flex;
            gap: 35px;
            align-items: flex-start;
        }
        .form-section {
            width: 35%;
        }
        .table-section {
            width: 65%;
        }
        h2 {
            font-size: 15px;
            margin-bottom: 15px;
            color: #222;
        }
        label {
            display: block;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        input {
            width: 100%;
            height: 32px;
            padding: 7px 9px;
            margin-bottom: 13px;
            border: 1px solid #d0d0d0;
            border-radius: 3px;
            font-size: 12px;
            outline: none;
        }
        input:focus {
            border-color: #1677ff;
        }
        .submit-btn {
            width: 100%;
            height: 32px;
            border: none;
            border-radius: 3px;
            background: #0879f9;
            color: white;
            font-size: 11px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background: #066bdc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th {
            background: #f5f5f5;
            font-weight: bold;
            text-align: left;
            padding: 9px 7px;
            border-bottom: 1px solid #ddd;
        }
        td {
            padding: 8px 7px;
            border-bottom: 1px solid #eee;
        }
        .edit-btn {
            background: #24b999;
            color: #222;
            padding: 4px 7px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 10px;
        }
        .delete-btn {
            background: #dc3545;
            color: white;
            padding: 4px 7px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 10px;
        }
        .alert {
            padding: 9px;
            margin-bottom: 20px;
            border-radius: 3px;
            font-size: 12px;
            text-align: center;
        }
        .success {
            background: #d1e7dd;
            color: #0f5132;
        }
        .danger {
            background: #f8d7da;
            color: #842029;
        }
        .no-users {
            text-align: center;
            color: #888;
            padding: 15px;
        }
        @media (max-width: 700px) {
            .content {
                flex-direction: column;
            }
            .form-section,
            .table-section {
                width: 100%;
            }
            .container {
                margin: 30px auto;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>User Management System</h1>

    <?php if ($message != ""): ?>
        <div class="alert <?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="content">
        <div class="form-section">
            <h2>Add New User</h2>
            <form method="POST" action="index.php">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter Full Name" required>

                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter Email Address" required>

                <button type="submit" class="submit-btn">Submit</button>
            </form>
        </div>

        <div class="table-section">
            <h2>Registered Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["id"]); ?></td>
                            <td><?php echo htmlspecialchars($row["name"]); ?></td>
                            <td><?php echo htmlspecialchars($row["email"]); ?></td>
                            <td><?php echo date("M d, Y", strtotime($row["Joined"])); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $row["id"]; ?>" class="edit-btn">Edit</a>
                                <a href="delete.php?id=<?php echo $row["id"]; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-users">No data found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>