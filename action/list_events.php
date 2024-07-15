<?php
require "../../sso/common.php";
require "../creds.php";
require "common.php";

validate_token("https://infotoast.org/rsvp/action/list_events.php");
$uid = get_user_id();

$conn = mysqli_connect(get_database_host(), get_database_user(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    http_response_code(500);
    die("dbconn");
}

$sql = $conn->prepare("SELECT * FROM events WHERE user_id = ?;");
$sql->bind_param("i", $uid);
$sql->execute();

echo '{"events":[';
$count = 0;

if ($result = $sql->get_result()) {
    while ($row = $result->fetch_assoc()) {
        if ($count != 0) {
            echo ", ";
        }
        echo "{\"id\": " . $row["id"] . ", ";
        echo "\"name\": \"" . $row["name"] . "\", ";
        echo "\"location\": \"". $row["location"] . "\", ";
        echo "\"date\": \"" . $row["event_date"] . "\", ";
        echo "\"description\": \"" . $row["description"] . "\"}";
        $count++;
    }
}

echo "], \"count\": $count}";
