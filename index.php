<?php
require "../sso/common.php";
require "creds.php";
require "action/common.php";

validate_token("https://infotoast.org/rsvp/");
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
    <script type="text/javascript" src="resources/js/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="resources/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/sso/resources/node_modules/js-cookie/dist/js.cookie.min.js"></script>
    <script type="text/javascript" src="/sso/resources/login-box.js"></script>
    <script type="text/javascript" src="resources/js/index.js"></script>
    <title>RSVP - Your Events</title>
</head>
<body>
<div class="top">
    <div class="topleft">
        <h1>Info Toast RSVP</h1>
    </div>
    <div class="topright">
            <div class="loginbutton"></div>
    </div>
</div>
<div class="theBody">
    <div class="iconBodyHeader" id="firstHeader">
        <h2>➕ Create New Event:</h2>
    </div>
    <form id="createEventForm" action="action/create_event.php" method="post">
        <label for="eventNameBox">Event Name: </label>
        <input type="text" name="name" id="eventNameBox" placeholder="Event name here..."><br>
        <label for="eventDateBox" id="eventDateBox">Event Date: </label>
        <input type="text" name="date" id="date" placeholder="Write Date in your format here..."><br>
        <label for="eventLocationBox">Event Location (Optional): </label>
        <input type="text" name="location" id="eventLocationBox" placeholder="Location of event"><br>
        <label for="descriptionBox">Add A Description (Optional): </label>
        <textarea name="description" id="descriptionBox" rows="4" cols="40" placeholder="Add an optional message"></textarea>
        <br>
        <input type="submit" value="Create Event" name="submit" id="submitBtn">
    </form>
    <div class="iconBodyHeader">
        <h2>🎉 Your Events:</h2>
    </div>
    <div class="events">
        <ul id="eventsList">
        </ul>
    </div>
</div>
</body>
</html>
