<?php
require "../../sso/common.php";
require "../creds.php";
require "common.php";

if (!isset($_GET["event_id"])) {
    http_response_code(400);
    die("noinfo");
}
$event_id = $_GET["event_id"];

validate_token("https://infotoast.org/rsvp/action/get_attendees_info.php");
$uid = get_user_id();

$conn = mysqli_connect(get_database_host(), get_database_user(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    http_response_code(500);
    die("dbconn");
}

// Validate whether user owns event
$sql = $conn->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?;");
$sql->bind_param("ii", $event_id, $uid);
$sql->execute();
$passed_check = false;
if ($result = $sql->get_result()) {
    while ($row = $result->fetch_assoc()) {
        $passed_check = $row["id"] == $event_id;
    }
}

if (!$passed_check) {
    http_response_code(403);
    die("unowned");
}

$sql2 = $conn->prepare("SELECT * FROM attendees WHERE event_id = ?;");
$sql2->bind_param("i", $event_id);
$sql2->execute();

$count_responded = 0;
$count_not_responded = 0;
$count_yes = 0;
$count_no = 0;
$count_maybe = 0;
$count_unanswered = 0;

echo "{\"responses\": [";
$count = 0;

if ($result2 = $sql2->get_result()) {
    while ($row = $result2->fetch_assoc()) {
        if ($count != 0) {
            echo ", ";
        }

        echo "{\"name\": \"" . $row["name"] . "\", ";
        echo "\"date_added\": \"" . $row["date_added"] . "\", ";
        echo "\"accessed\": " . $row["accessed"] . ", ";
        if ($row["accessed"]) {
            $count_responded++;
            echo "\"time_accessed\": \"" . $row["time_accessed"] . "\", ";
            echo "\"country_code\": \"" . $row["country_code"] . "\", ";
        } else {
            $count_not_responded++;
        }
        echo "\"response\": " . $row["response"] . ", ";
        if ($row["response"] != 0) {
            if ($row["response"] == 1) {
                $count_yes++;
            } else if ($row["response"] == 2) {
                $count_no++;
            } else if ($row["response"] == 3) {
                $count_maybe++;
            }
            if ($row["response_note"] != null && $row["response_note"] != "") {
                echo "\"response_note\": \"" . $row["response_note"] . "\"";
            } else {
                echo "\"response_note\": null";
            }
        } else {
            $count_unanswered++;
        }
        echo "}";
        $count++;
    }
}

$conn->close();

echo "], \"total\": " . $count . ", ";
echo "\"responded\": " . $count_responded . ", ";
echo "\"not_responded\": " . $count_not_responded . ", ";
echo "\"yes\": " . $count_yes . ", ";
echo "\"no\": " . $count_no . ", ";
echo "\"maybe\": " . $count_maybe . ", ";
echo "\"unanswered\": " . $count_unanswered;
echo "}";


