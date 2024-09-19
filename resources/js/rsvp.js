function submit_response(response, message, event_id, attendee_id) {
    function return_response(data, status) {
        console.log(data);
        console.log(status);
        if (data.startsWith("success")) {
            window.location.replace("https://infotoast.org/rsvp/thanks.html");
        }
    }
    if (message == null) {
        $.post("https://infotoast.org/rsvp/action/respond.php", {event_id: event_id, attendee_id: attendee_id, response: response}, return_response);
    } else {
        $.post("https://infotoast.org/rsvp/action/respond.php", {event_id: event_id, attendee_id: attendee_id, response: response, message: message}, return_response);
    }
}

function get_response_data(response) {
    var message_text = document.getElementById("message").value;
    if (message_text == "") {
        message_text = null;
    }
    let event_id = document.getElementById("event_id").value;
    let attendee_id = document.getElementById("attendee_id").value;
    console.log(event_id);
    console.log(attendee_id);
    submit_response(response, message_text, event_id, attendee_id);
}

$(document).ready(function() {
    /*$("#yesbtn").click(function() {
        get_response_data(1);
    });
    $("#nobtn").click(function() {
        get_response_data(2);
    });
    $("#maybebtn").click(function() {
        get_response_data(3);
    });*/
});