function submit_response(response, message, event_id, attendee_id) {
    function return_response(data, status) {
        if (status == 200) {
            if (data.startsWith("success")) {
                window.location.replace("https://infotoast.org/rsvp/thanks.html");
            }
        }
    }
    if (message == null) {
        $.post("https://infotoast.org/rsvp/action/respond.php", {event_id: event_id, attendee_id: attendee_id, response: response}, return_response);
    } else {
        $.post("https://infotoast.org/rsvp/action/respond.php", {event_id: event_id, attendee_id: attendee_id, response: response, message: message}, return_response);
    }
}

$(document).ready(function() {
    function get_response_data(response) {
        var message_text = document.getElementById("message").value;
        if (message_text == "") {
            message_text = null;
        }
        let event_id = document.getElementById("event_id").value;
        let attendee_id = document.getElementById("attendee_id").value;
        submit_response(response, message_text, event_id, attendee_id);
    }
    $("#yesbtn").click(function() {
        get_response_data(1);
    });
    $("#nobtn").click(function() {
        get_response_data(2);
    });
    $("#maybebtn").click(function() {
        get_response_data(3);
    });
});