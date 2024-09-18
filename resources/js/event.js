var global_attendees = [];
var attendees_count;

function parseAttendeesJSON(responseJson) {
    global_attendees = responseJson.responses;
    attendees_count = responseJson.total;
}

function display_attendees() {
    for (let i = 0; i < attendees_count; i++) {
        display_attendee(global_attendees[i]);
    }
}

function display_attendee(attendee) {
    let attendee_name = attendee.name;
    let attendee_token = attendee.token;
    let attendee_accessed = attendee.accessed;
    let attendee_time_accessed = null;
    let attendee_country = null;
    if (attendee_accessed) {
        attendee_time_accessed = attendee.time_accessed;
        attendee_country = attendee.country_code;
    }
    let attendee_response = attendee.response;
    let attendee_response_note = attendee.response_note;
    print_attendee(attendee_name, attendee_token, attendee_accessed, attendee_time_accessed, attendee_country, attendee_response, attendee_response_note);
}

function add_attendee(name, token, date_added) {
    attendees_count++;
    global_attendees.push({
        name: name,
        token: token,
        date_added: date_added,
        accessed: 0,
        time_accessed: null,
        country: null,
        response: 0,
        response_note: null
    });
}

function get_response_human_readable(response) {
    switch (response) {
        case 0:
            return "No response yet";
        case 1:
            return "Responded Yes";
        case 2:
            return "Responded No";
        case 3:
            return "Responded Maybe";
    }
}

function copy_link(token) {
    let textField = document.getElementById("linkCopy_" + token);
    textField.select();
    textField.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(textField.value);
    $("#clickCopy_" + token).text("Copied!");
    setTimeout(function() {
        $("#clickCopy_" + token).text("Click to copy link:");
    }, 2000);
}

function print_attendee(name, token, accessed, time_accessed, country, response, note) {
    var accessedString;
    if (accessed) {
        accessedString = "Accessed in " + country + " at " + time_accessed;
    } else {
        accessedString = "RSVP page not accessed yet";
    }
    let response_readable = get_response_human_readable(response);
    var responseNoteString;
    if (note != null) {
        responseNoteString = "<a class='responseNoteLink' href='javascript:display_note(" + note + ")'>Read Response Note</a>";
    } else {
        responseNoteString = "No note left.";
    }
    const toAppend = "<div class='event'><li>" +
        "<div class='eventSeperator eventNameDesc'><span class='eventName'>" + name + "</span><br><br><span class='eventLoc'>" + accessedString + "</span></div>" +
        "<div class='eventSeperator'><span class='response'>" + response_readable + "</span></div>" +
        "<div class='eventSeperator'><span class='responseNoteLinkSpan'>" + responseNoteString + "</span></div>" +
        "<div class='eventSeperator'><span class='clickToCopy' id='clickCopy_" + token + "'>Click to copy link:</span><br><br><input type='text' id='linkCopy_" + token + "' class='linkCopyText' value='https://infotoast.org/rsvp/rsvp.php?t=" + token + "' onclick='copy_link(" + token + ")'</div>";
    $("#attendeesList").append(toAppend);
}

function edit_event() {
    $("#notEditingEvtDetails").hide();
    $("#editingEvtDetails").show();
}

function send_invite() {
    let inviteeName = document.getElementById("inviteeName").value;
    let inviteeEmail = document.getElementById("inviteeEmail").value;
    const event_id = document.getElementById("eventIdHiddenField").value;

    $.post("https://infotoast.org/rsvp/action/add_attendee.php", {event_id: event_id, name: inviteeName, email: inviteeEmail}, function(data, status) {
        if (status == 200) {
            if (data.startsWith("success")) {
                let splitResponse = data.split(",");
                let new_token = splitResponse[2];
                add_attendee(inviteeName, new_token, "Now");
                display_attendee(global_attendees[attendees_count-1]);
            }
        }
    });
}

$(document).ready(function() {
    $("#editingEvtDetails").hide();

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState == 4) {
            if (this.status == 200) {
                parseAttendeesJSON(this.responseText);
                display_attendees();
            }
        }
    };
    const event_id = document.getElementById("eventIdHiddenField").value;
    xhr.open("GET", "https://infotoast.org/rsvp/action/get_attendees_info.php?event_id=" + event_id, true);
    xhr.send();
});