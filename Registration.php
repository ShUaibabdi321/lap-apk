<?php

// Database connection
$conn = new mysqli('localhost', 'root', '', 'registration');
if ($conn->connect_error) {
    echo "$conn->connect_error";
    die("Connection Failed: " . $conn->connect_error);
}

if (isset($_POST['fullName']) && isset($_POST['gender']) && isset($_POST['email'])) {
    $fullName = $_POST['fullName'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];

    // Server-side validation
    $errors = [];
    if (empty($fullName)) {
        $errors[] = "Full name is required";
    }
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (empty($gender)) {
        $errors[] = "Gender is required";
    } elseif (!in_array($gender, ['Male', 'Female'])) {
        $errors[] = "Invalid gender value";
    }

    if (empty($errors)) {
        // Insert data into database
        $stmt = $conn->prepare("INSERT INTO st_reg(fullName, gender, email) VALUES(?, ?, ?)");
        $stmt->bind_param("sss", $fullName, $gender, $email);
        $stmt->execute();
        echo "Registration successfully...";
        $stmt->close();
    } else {
        // Display error messages
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}

// List registered students
echo "<h2>Registered Students</h2>";
$result = $conn->query("SELECT * FROM st_reg");
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Full Name</th><th>Gender</th><th>Email</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['fullName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No registered students found</p>";
}

$conn->close();
