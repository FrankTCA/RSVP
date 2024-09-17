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
        $sql2 = $conn->prepare("SELECT COUNT(id) as count FROM attendees WHERE event_id = ? AND accessed = 1;");
        $sql2->bind_param("i", $row["id"]);
        $sql2->execute();
        echo "{\"id\": " . $row["id"] . ", ";
        echo "\"name\": \"" . $row["name"] . "\", ";
        echo "\"location\": \"". $row["location"] . "\", ";
        echo "\"date\": \"" . $row["event_date"] . "\", ";
        echo "\"description\": \"" . $row["description"] . "\"";
        if ($result2 = $sql2->get_result()) {
            while ($row2 = $result2->fetch_assoc()) {
                echo ", \"responses\": " . $row2["count"];
                $sql3 = $conn->prepare("SELECT COUNT(id) as count FROM attendees WHERE event_id = ?;");
                $sql3->bind_param("i", $row["id"]);
                $sql3->execute();
                if ($result3 = $sql3->get_result()) {
                    while ($row3 = $result3->fetch_assoc()) {
                        echo ", \"attendees\": " . $row3["count"];
                    }
                } else {
                    echo ", \"attendees\": 0";
                }
            }
        } else {
            echo ", \"responses\": 0, \"attendees\": 0";
        }
        echo "}";
        $count++;
    }
}

$conn->close();

echo "], \"count\": $count}";
