<?php
if(empty($id)) {
    error('You cannot call this script in that way');
}
if (!isset($currenttab)) {
    $currenttab = '';
}

$tabs = array();
$row = array();
$activated = array();
$inactive = array();

$row[] = new tabobject('futurebookings', "$CFG->wwwroot/my/bookings.php?id=$id",
                           get_string('tab:futurebookings', 'local'));
$row[] = new tabobject('pastbookings', "$CFG->wwwroot/my/pastbookings.php?id=$id",
                           get_string('tab:pastbookings', 'local'));
$tabs[] = $row;

$activated[] = $currenttab;
print_tabs($tabs, $currenttab);

