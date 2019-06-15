# Attendance

## What this module sets out to do

*Attendance* implements a custom entity type to register attendance by users to event (date-field-enabled) nodes.
The Attendance entity supports bundles for different node types and is itself fully fieldable.
By default, Attendance entities have a user id (attendee), email (in case of anonymous attendees), a node id (the event node) and a boolean public status.

## TO DO

*Attendance* bundles already support picking a geofield from the event node to geofence the attendance form submissions.
It still lacks the actual geofencing implementation.

Further development includes:

* attendance blocks (ajax-enabled entity forms in block)
* client-side geofencing
* proper permission settings
* supporting user geofields for logged-in users
* views-enabled attendance reports per event
