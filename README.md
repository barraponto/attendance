# Attendance

## What this module sets out to do

*Attendance* implements a custom entity type to register attendance by users to (event) nodes.
The Attendance entity supports bundles targeting different node types and is itself fully fieldable.
By default, Attendance entities have a user id (attendee), email (in case of anonymous attendees), a node id (the attended node) and a boolean public status.

## FEATURES

* attendance form blocks
* proper permission settings
* views-based attendance reports per node
* views-based attendance listings in blocks

## INSTRUCTIONS

Visit `/admin/structure/attendance` to create attendance types (like "guests" or "vip").
Choose node types to attend to (usually event nodes).
Then place the corresponding form block in the block layout and make sure it shows up in (event) node pages.
It should be visible to users with the permission to add attendance entries for that attendance type.
The node owner should be able to see the attendance report as a tab in his node page.
There are also public attendance listing blocks available.

## TODO

* enable ajax in attendance form blocks
