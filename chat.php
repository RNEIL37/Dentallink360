<?php
session_start();
$conn = new mysqli("localhost", "root", "", "chat");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database error"]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$my_id = $_SESSION['user_id'];
$receiver = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

/* USERS */
$users = [];
$res = $conn->query("SELECT id, username FROM users WHERE id != $my_id");
while ($u = $res->fetch_assoc()) {
    $users[] = $u;
}

/* RECEIVER NAME */
$receiver_name = "Select a user";
if ($receiver > 0) {
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $receiver);
    $stmt->execute();
    $receiver_name = $stmt->get_result()->fetch_assoc()['username'] ?? "Unknown";
}

/* MESSAGES */
$messages = [];
if ($receiver > 0) {
    $stmt = $conn->prepare("
        SELECT sender_id, message
        FROM messages
        WHERE (sender_id = ? AND receiver_id = ?)
           OR (sender_id = ? AND receiver_id = ?)
        ORDER BY id ASC
    ");
    $stmt->bind_param("iiii", $my_id, $receiver, $receiver, $my_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $messages[] = $row;
    }
}

echo json_encode([
    "my_id" => $my_id,
    "receiver_id" => $receiver,
    "receiver_name" => $receiver_name,
    "users" => $users,
    "messages" => $messages
]);
