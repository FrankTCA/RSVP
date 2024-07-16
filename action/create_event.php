<?php
require "../../sso/common.php";
require "../creds.php";
require "common.php";

if (!(isset($_POST["name"]) && isset($_POST["date"]) && isset($_POST["description"]))) {
    http_response_code(400);
    die("noinfo");
}

validate_token("https://infotoast.org/rsvp/action/create_event.php");

$uid = get_user_id();

$conn = mysqli_connect(get_database_host(), get_database_user(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    http_response_code(500);
    die("dbconn");
}

$event_name = $_POST["name"];
$event_date_str = $_POST["date"];
$event_location = $_POST["location"];
$event_description = $_POST["description"];

# Date must be in proper format
if (!validate_date($event_date_str)) {
    http_response_code(400);
    die("baddate");
}

$sql = $conn->prepare("INSERT INTO events (user_id, name, event_date, location, description) VALUES (?, ?, ?, ?, ?);");
$sql->bind_param("issss", $uid, $event_name, $event_date_str, $event_location, $event_description);
$sql->execute();

$lastSQLId = $conn->prepare("SELECT LAST_INSERT_ID();");
$lastSQLId->execute();

$event_id = null;

if ($result = $lastSQLId->get_result()) {
    while ($row = $result->fetch_assoc()) {
        $event_id = $row["LAST_INSERT_ID()"];
    }
}

$conn->close();

if ($event_id === null) {
    http_response_code(500);
    die("Query did not properly perform!");
}

header("Location: https://infotoast.org/rsvp/event.php?id=" . $event_id);
