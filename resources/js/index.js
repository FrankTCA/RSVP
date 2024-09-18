var global_events = [];
var events_count;

function parseEventsJSON(responseJson) {
    global_events = responseJson["events"];
    events_count = responseJson["count"];
}

function display_events() {
    for (var i = 0; i < events_count; i++) {
        display_event(global_events[i]);
    }
}

function get_event_by_id(id) {
    for (var i = 0; i < events_count; i++) {
        if (global_events[i].id == id) {
            return global_events[i];
        }
    }
}

function open_event(id) {
    window.location.replace("https://infotoast.org/rsvp/event.php?id=" + id);
}

function display_event(event) {
    let evt_id = event.id;
    let evt_name = event.name;
    let evt_loc = event.location;
    let evt_date = event.date;
    let evt_desc = event.description;
    let evt_responses = event.responses;
    let evt_attendees = event.responses;
    print_event(evt_id, evt_name, evt_loc, evt_date, evt_desc, evt_responses, evt_attendees);
}

function print_event(id, name, location, date, description, responses, attendees) {
    const toAppend = "<div class='event' id='event_" + id + "'><li>" +
        "<div class='eventSeperator'><button onclick='open_event(" + id + ")' class='eventbtn'>📬</button></div>" +
        "<div class='eventNameDesc eventSeperator'><span class='eventName'>" + name + "</span><br><br><span class='eventLoc'>" + location + "</span></div>" +
        "<div class='eventSeperator'><span class='date'>" + date + "</span></div>" +
        "<div class='eventSeperator'><span class='responsesText'><span class='responsesNumber'>" + responses + "</span> of <span class='responsesNumber'>" + attendees + "</span> attendees have responded</span></div>" +
        "</li></div>";
    $("#eventsList").append(toAppend);
}

$(document).ready(function() {
    $("#date").datepicker();
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState === 4) {
            if (this.status === 200) {
                let eventsJson = JSON.parse(this.responseText);
                parseEventsJSON(eventsJson);
                display_events();
            }
        }
    }
    xhr.open("GET", "https://infotoast.org/rsvp/action/list_events.php", true);
    xhr.send();
});
