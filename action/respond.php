<?php
if (!(isset($_POST["event_id"]) && isset($_POST["attendee_id"]) && isset($_POST["response"]))) {
    http_response_code(400);
    die("noinfo");
}

$event_id = $_POST["event_id"];
$attendee_id = $_POST["attendee_id"];
$response = $_POST["response"];
$message = $_POST["message"];

if ($message != null) {
    $message = htmlspecialchars($message);
}

if ($response < 1 || $response > 3) {
    http_response_code(400);
    die("baddata");
}

$conn = mysqli_connect(get_database_host(), get_database_user(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    http_response_code(500);
    die("dbconn");
}

$check_event_sql = $conn->prepare("SELECT * FROM attendees WHERE id = ? AND event_id = ?;");
$check_event_sql->bind_param("ii", $attendee_id, $event_id);
$check_event_sql->execute();

$check_passed = false;

if ($result = $check_event_sql->get_result()) {
    while ($row = $result->fetch_assoc()) {
        $check_passed = true;
    }
}

if (!$check_passed) {
    http_response_code(403);
    die("nomatch");
}

$sql = $conn->prepare("UPDATE attendees SET time_accessed = CURRENT_TIMESTAMP, response = ?, response_note = ? WHERE id = ?;");
$sql->bind_param("isi", $response, $response_message, $attendee_id);
$sql->execute();

$conn->close();

echo "success";
