<?php
require "../../sso/common.php";
require "../creds.php";
require "common.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (!(isset($_POST["event_id"]) && isset($_POST["name"]))) {
    http_response_code(400);
    die("noinfo");
}

$event_id = $_POST["event_id"];
$attendee_name = $_POST["name"];
$attendee_email = $_POST["email"];

validate_token("https://infotoast.org/rsvp/action/add_attendee.php");
$uid = get_user_id();

$conn = mysqli_connect(get_database_host(), get_database_user(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    http_response_code(500);
    die("dbconn");
}

$event_name = "";
$event_date = "";

// Make sure event is owned by user
$sql = $conn->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?;");
$sql->bind_param("ii", $event_id, $uid);
$sql->execute();
$passed_check = false;
if ($result = $sql->get_result()) {
    while ($row = $result->fetch_assoc()) {
        $passed_check = $row["id"] == $event_id;
        $event_name = $row["name"];
        $event_date = $row["event_date"];
    }
}

if (!$passed_check) {
    http_response_code(403);
    die("unowned");
}

$token_str = random_bytes(16);

$sql2 = $conn->prepare("INSERT INTO attendees (event_id, name, email, token) VALUES (?, ?, ?, LEFT(SHA2(?, 256), 10));");
$sql2->bind_param("isss", $event_id, $attendee_name, $attendee_email, $token_str);
$sql2->execute();

$attendee_data_sql = $conn->prepare("SELECT id,token FROM attendees WHERE id = LAST_INSERT_ID() AND event_id = ?;");
$attendee_data_sql->bind_param("i", $event_id);
$attendee_data_sql->execute();

$token = null;
$attendee_id = null;

if ($result = $attendee_data_sql->get_result()) {
    while ($row = $result->fetch_assoc()) {
        $attendee_id = $row["id"];
        $token = $row["token"];
    }
}

$conn->close();

$rsvp_link = "https://infotoast.org/rsvp/rsvp.php?t=$token";

if ($attendee_email != null) {
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->Username = "no-reply@infotoast.org";
    $mail->Password = get_noreply_password();
    $organizer_email = get_user_email();
    $mail->setFrom("no-reply@infotoast.org", "$organizer_email via Info Toast RSVP");
    $mail->addAddress($attendee_email);
    $mail->addReplyTo($organizer_email);
    $mail->isHTML(true);

    $mail->Subject = "RSVP to $event_name";
    $mail->Body = "<h1>You have been invited to $event_name</h1><br><br><h3>Please RSVP to <a href='mailto:$organizer_email'>$organizer_email</a></h3><br><br><p><strong>You can RSVP with <a href='$rsvp_link'>our online tool here.</a></strong></p><p>If the above link does not work, please copy and paste $rsvp_link in your browser.</p>";
    $mail->AltBody = "You have been invited to $event_name at $event_date. Please RSVP to $organizer_email with our online tool here: $rsvp_link";
    $mail->send();
}

echo "success,$attendee_id,$token";
