<?php
// add_contact.php
// DB credentials
$host = 'localhost';
$user = 'root';
$pass = '!Rr201612066';
$db   = 'contacts_list';

// Connect
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product  = $_POST['product'];
    $supplier = $_POST['supplier'];
    $end_date = $_POST['end_date'];  // YYYY-MM-DD
    $email    = $_POST['email'];

    $stmt = $conn->prepare(
        "INSERT INTO contacts (product, supplier, end_date, email) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $product, $supplier, $end_date, $email);
    if ($stmt->execute()) {
        // Redirect back to contacts list
        header("Location: Contacts list.html");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
