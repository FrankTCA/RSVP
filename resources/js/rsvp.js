function submit_response(response, message, event_id, attendee_id) {

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