<?php
require "../sso/common.php";
require "creds.php";
require "action/common.php";

if (!isset($_GET["id"])) {
    http_response_code(400);
    die("Event ID must be provided!");
}

$event_id = $_GET["id"];

validate_token("https://infotoast.org/rsvp/event.php");
$uid = get_user_id();
$username = get_username();

$conn = mysqli_connect(get_database_host(), get_database_user(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    http_response_code(500);
    die("Error connecting to database! Please email frank@infotoast.org.");
}

// Get event details
$event_details_sql = $conn->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?;");
$event_details_sql->bind_param("ii", $event_id, $uid);
$event_details_sql->execute();

$event_name = null;
$event_date = null;
$event_location = null;
$date_created = null;
$description = null;

if ($result = $event_details_sql->get_result()) {
    while ($row = $result->fetch_assoc()) {
        $event_name = $row["name"];
        $event_date = $row["event_date"];
        $event_location = $row["location"];
        $date_created = $row["date_created"];
        $description = $row["description"];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="resources/css/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="/global-resources/mallory.css">
    <link rel="stylesheet" type="text/css" href="/sso/resources/login-box.css">
    <link rel="stylesheet" type="text/css" href="resources/css/global.css">
    <link rel="stylesheet" type="text/css" href="resources/css/module.css">
    <link rel="stylesheet" type="text/css" href="resources/css/events.css">
    <link rel="stylesheet" type="text/css" href="resources/css/event.css">
    <script type="text/javascript" src="resources/js/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="resources/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/sso/resources/node_modules/js-cookie/dist/js.cookie.min.js"></script>
    <script type="text/javascript" src="/sso/resources/login-box.js"></script>
    <script type="text/javascript" src="resources/js/event.js"></script>
    <title>Viewing <?php echo $event_name ?></title>
</head>
<body>
<div class="top">
    <div class="topleft">
        <h1><a href="index.php">◀ Info Toast RSVP</a></h1>
    </div>
    <div class="topright">
            <div class="loginbutton"></div>
    </div>
</div>
<div class="theBody">
    <div class="iconBodyHeader" id="firstHeader">
        🗒️ Event Details
    </div>
    <div class="eventDetails" id="notEditingEvtDetails">
        <p>Event Name: <strong><?php echo $event_name ?></strong></p>
        <p>Event Date/Time: <strong><?php echo $event_date ?></strong></p>
        <p>Event Location: <strong><?php echo $event_location ?></strong></p>
        <p>You made event at: <strong><?php echo $date_created ?></strong></p>
        <p>Additional Description: <strong><?php echo $description ?></strong></p>
        <div class="editBtnDiv">
            <button class="editBtn" id="editEventButton" onclick="edit_event()">Edit</button>
        </div>
    </div>
    <div class="eventDetails" id="editingEvtDetails">
        <form action="action/edit_event.php" method="POST">
            <input id="eventIdHiddenField" type="hidden" name="id" value="<?php echo $event_id ?>">
            <p>Event Name: <input type="text" value="<?php echo $event_name ?>" name="name"</p>
            <p>Event Date/Time: <input type="text" value="<?php echo $event_date ?>" name="date"</p>
            <p>Event Location: <input type="text" value="<?php echo $event_location ?>" name="location"</p>
            <p>You made event at: <strong><?php echo $date_created ?></strong></p>
            <p>Additional Description: <textarea name="description" rows="4" cols="40"><?php echo $description ?></textarea></p>
            <div class="editBtnDiv">
                <input type="submit" class="editBtn" name="submit" id="editEvtSubmitButton" value="Edit">
            </div>
        </form>
    </div>
    <div class="iconBodyHeader">
        ➕ Invite New Attendees
    </div>
    <div class="inviteAttendeesForm">
        <p><label for="inviteeName">Name: </label><input type="text" id="inviteeName" name="inviteeName" placeholder="Name"></p>
        <p><label for="inviteeEmail">Email (Optional, will send email to invitee if specified): </label><input type="text" id="inviteeEmail" name="inviteeEmail" placeholder="invitee@example.com"</p>
        <div class="editBtnDiv">
            <button class="editBtn" id="inviteButton" onclick="send_invite()">Invite</button>
        </div>
    </div>
    <div class="iconBodyHeader">
        👤 Attendees
    </div>
    <ul class="attendeesList" id="attendeesList">
    </ul>
</div>
</body>
</html>
