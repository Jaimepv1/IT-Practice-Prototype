<?php
$host = "localhost";
$user = "root";
$password = "enter your own password";
$dbname = "contacts_list";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT product, supplier, DATE_FORMAT(end_date, '%d/%m/%Y') as end_date, email FROM contacts";
$result = $conn->query($sql);

$contacts = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $contacts[] = $row;
    }
}

echo json_encode($contacts);

$conn->close();
?>
