<?php
require "../../sso/common.php";
require "../creds.php";
require "common.php";

if (!isset($_POST["id"])) {
    http_response_code(400);
    die("Not enough parameters. Please include at least the ID of the event to edit.");
}

$id = $_POST["id"];
$name = $_POST["name"];
$description = $_POST["description"];
$date = $_POST["date"];
$location = $_POST["location"];

validate_token("https://infotoast.org/rsvp/action/edit_event.php");
$uid = get_user_id();

$conn = mysqli_connect(get_database_host(), get_database_user(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    die("Could not connect to database! Please email frank@infotoast.org if you believe this is an error.");
}

$verify_sql = $conn->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?");
$verify_sql->bind_param("ii", $id, $uid);
$verify_sql->execute();

$verified = false;

if ($result = $verify_sql->get_result()) {
    while ($row = $result->fetch_assoc()) {
        $verified = true;
    }
}

if (!$verified) {
    http_response_code(403);
    die("You are requesting an event that does not belong to you!");
}

$edit_sql = $conn->prepare("UPDATE events SET name = ?, description = ?, event_date = ?, location = ? WHERE id = ?;");
$edit_sql->bind_param("ssssi", $name, $description, $date, $location, $id);
$edit_sql->execute();

$conn->close();

http_response_code(302);
header("Location: https://infotoast.org/rsvp/event.php?id=" . $id);
die();
