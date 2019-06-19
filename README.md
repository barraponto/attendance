# Attendance

## What this module sets out to do

*Attendance* implements a custom entity type to register attendance by users to (event) nodes.
The Attendance entity supports bundles for different node types and is itself fully fieldable.
By default, Attendance entities have a user id (attendee), email (in case of anonymous attendees), a node id (the attended node) and a boolean public status.

# FEATURES

* attendance form blocks
* views-based attendance reports per node
* views-based attedance listings in blocks

## TO DO

*Attendance* bundles already support picking a geofield from the node to geofence the attendance form submissions.
It still lacks the actual geofencing implementation.

Further development includes:

* ajaxifying attendance form blocks
* client-side geofencing
* proper permission settings
* supporting user geofields for logged-in users
