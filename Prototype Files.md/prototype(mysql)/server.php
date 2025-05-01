<?php
// server.php

// === Gmail SMTP credentials ===
const SMTP_SERVER   = 'ssl://smtp.gmail.com';
const SMTP_PORT     = 465;
const SMTP_USER     = 'happywilsonso@gmail.com';
const SMTP_PASSWORD = 'hyxptnynscazhdhh';

// === MySQL configuration ===
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '!Rr201612066';
$dbName = 'contacts_list';

// helper: read one line from socket and ensure we got the expected code
function smtp_get(&$socket, $expectedCode) {
    $line = fgets($socket, 515);
    if (substr($line,0,3) != $expectedCode) {
        throw new Exception("SMTP error: Expected {$expectedCode}, got {$line}");
    }
    return $line;
}

// Send mail via raw SMTP
function smtp_send($to, $subject, $body, $from) {
    $sock = fsockopen(SMTP_SERVER, SMTP_PORT, $errno, $errstr, 15);
    if (!$sock) throw new Exception("Connection failed: {$errno} {$errstr}");

    // handshake
    smtp_get($sock, '220');
    fwrite($sock, "EHLO localhost\r\n");
    // Gmail may respond with multiple lines; read until a line that doesn't start with "250-"
    do {
        $l = fgets($sock, 515);
    } while (substr($l,0,4) === "250-");
    if (substr($l,0,3) !== "250") {
        throw new Exception("EHLO failed: {$l}");
    }

    // auth
    fwrite($sock, "AUTH LOGIN\r\n");
    smtp_get($sock, '334');                          // username prompt
    fwrite($sock, base64_encode(SMTP_USER)."\r\n");
    smtp_get($sock, '334');                          // password prompt
    fwrite($sock, base64_encode(SMTP_PASSWORD)."\r\n");
    smtp_get($sock, '235');                          // auth success

    // mail envelope
    fwrite($sock, "MAIL FROM:<{$from}>\r\n");
    smtp_get($sock, '250');
    fwrite($sock, "RCPT TO:<{$to}>\r\n");
    smtp_get($sock, '250');

    // data
    fwrite($sock, "DATA\r\n");
    smtp_get($sock, '354');

    // headers + body
    $headers  = "From: {$from}\r\n";
    $headers .= "To: {$to}\r\n";
    $headers .= "Subject: {$subject}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    fwrite($sock, $headers . "\r\n" . $body . "\r\n.\r\n");
    smtp_get($sock, '250');

    // quit
    fwrite($sock, "QUIT\r\n");
    smtp_get($sock, '221');

    fclose($sock);
}

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die('MySQL Connect Error: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // sanitize & validate
    $to      = filter_var($_POST['recipient_email'] ?? '', FILTER_VALIDATE_EMAIL);
    $subject = trim($_POST['subject'] ?? '');
    $body    = trim($_POST['message'] ?? '');
    if (! $to || $subject === '' || $body === '') {
        die('❌ Invalid input.');
    }

    try {
        smtp_send($to, $subject, $body, SMTP_USER);

        // log into MySQL
        $stmt = $conn->prepare(
            "INSERT INTO sent_emails (recipient, subject, message) VALUES (?, ?, ?)"
        );
        $stmt->bind_param('sss', $to, $subject, $body);
        $stmt->execute();
        $stmt->close();

        // redirect back to form
        header('Location: FormEmail.php');
        exit;
    }
    catch (Exception $e) {
        echo '❌ Error sending email: ' . htmlspecialchars($e->getMessage());
    }
}

$conn->close();
