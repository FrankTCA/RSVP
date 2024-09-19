<?php
require "creds.php";
require "action/common.php";

if (!isset($_GET["t"])) {
    http_response_code(400);
    die("Token not set! Please copy the whole link, including the text after ?t=");
}

$token = $_GET["t"];

$conn = mysqli_connect(get_database_host(), get_database_user(), get_database_password(), get_database_db());
if ($conn->connect_error) {
    die("Connection to database failed! Please contact our server admin: frank@infotoast.org");
}

$sql = $conn->prepare("SELECT t2.id as event_id, t1.id as attendee_id, t2.name, t2.event_date, t2.description, t1.email, t2.location FROM attendees t1 INNER JOIN events t2 ON t1.event_id = t2.id WHERE t1.token = ?;");
$sql->bind_param("s", $token);
$sql->execute();

$event_id = null;
$attendee_id = null;
$event_name = null;
$event_date = null;
$event_description = null;
$attendee_email = null;
$event_location = null;

if ($result = $sql->get_result()) {
    while ($row = $result->fetch_assoc()) {
        $event_id = $row["event_id"];
        $attendee_id = $row["attendee_id"];
        $event_name = $row["name"];
        $event_date = $row["event_date"];
        $event_description = $row["description"];
        $attendee_email = $row["email"];
        $event_location = $row["location"];
    }
}

if ($event_id = null) {
    http_response_code(403);
    die("Not a valid token! Make sure the token is complete, and 10 characters long.");
}

$get_info_sql = $conn->prepare("UPDATE attendees SET accessed = 1, ip_accessed = ?, country_code = ? WHERE id = ?;");
$ip = getUserIP();
$clientcountry = $_SERVER['HTTP_CF_IPCOUNTRY'];
$get_info_sql->bind_param("ssi", $ip, $clientcountry, $attendee_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="resources/css/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="resources/css/global.css">
    <link rel="stylesheet" type="text/css" href="resources/css/module.css">
    <link rel="stylesheet" type="text/css" href="resources/css/rsvp.css">
    <script type="text/javascript" src="resources/js/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="resources/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="resources/js/rsvp.js"></script>
    <title>RSVP to <?php echo $event_name?></title>
</head>
<body>
    <div class="top">
        <div class="topleft">
            <h1>Info Toast RSVP</h1>
        </div>
    </div>
    <div class="theBody">
        <div class="iconBodyHeader" id="firstHeader">
            <h2>🎉 Event Details</h2>
        </div>
        <h4><?php echo $event_name?></h4>
        <p>Event Date: <strong><?php echo $event_date ?></strong></p>
        <?php
        if ($event_location != null) {
            ?>
            <p>Event Location: <strong><?php echo $event_location ?></strong></p>
        <?php
        }
        if ($event_description != null) {
            ?>
            <div class="iconBodyHeader">
                <h2>💬 Details</h2>
            </div>
            <p><?php echo $event_description ?></p>
        <?php
        }
        ?>
        <div class="iconBodyHeader">
            <h2>📨 RSVP</h2>
        </div>
        <p><strong>Write a message (optional):</strong></p>
        <textarea id="message" name="message" rows="4" cols="40" placeholder="Message to Organizer..."></textarea><br><br>
        <input type="hidden" id="event_id" value="<?php echo $event_id ?>">
        <input type="hidden" id="attendee_id" value="<?php echo $attendee_id ?>">
        <p><strong>RSVP Response:</strong></p>
        <div class="btn-group">
            <button id="yesbtn">Yes</button>
            <button id="maybebtn">Maybe</button>
            <button id="nobtn">No</button>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
