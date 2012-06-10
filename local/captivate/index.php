<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/local/mitms.php');

$pagetitle = 'Help Videos';
$navlinks[] = array('name' => $pagetitle, 'link'=> '', 'type'=>'title');

$navigation = build_navigation($navlinks);

print_header_simple($pagetitle, '', $navigation, '', null, true);

// display heading
print_heading($pagetitle);

print '<p>These videos provide walk-through instructions on how to perform many administrative tasks within MITMS.</p>';

print '<ul style="list-style: none;">';
print '<li>' . mitms_captivate_popup('Assigning organisational details', 'assigning organisational details') . '</li>';
print '<li>' . mitms_captivate_popup('How to create a development plan', 'how to create a development plan') . '</li>';
print '<li>' . mitms_captivate_popup('How to enrol on a face to face session', 'how to enrol on a face to face session') . '</li>';
print '<li>' . mitms_captivate_popup('How to update a record of learning', 'how to update a record of learning') . '</li>';
print '<li>' . mitms_captivate_popup('Manager view', 'manager view') . '</li>';
print '<li>' . mitms_captivate_popup('Report builder', 'report builder') . '</li>';
print '<li>' . mitms_captivate_popup('Setting up a competency framework', 'setting up a competency framework') . '</li>';
print '<li>' . mitms_captivate_popup('Setting up a competency template', 'setting up a competency template') . '</li>';
print '<li>' . mitms_captivate_popup('Setting up activity completion conditions', 'setting up activity completion conditions') . '</li>';
print '<li>' . mitms_captivate_popup('Setting up a face to face activity', 'setting up a face to face activity') . '</li>';
print '<li>' . mitms_captivate_popup('Setting up competencies', 'setting up competencies') . '</li>';
print '<li>' . mitms_captivate_popup('Setting up competency scales', 'setting up competency scales') . '</li>';
print '<li>' . mitms_captivate_popup('Setting up course completion conditions', 'setting up course completion conditions') . '</li>';
print '<li>' . mitms_captivate_popup('Setting up custom categories and fields', 'setting up custom categories and fields') . '</li>';
print '<li>' . mitms_captivate_popup('Setting up depth levels', 'setting up depth levels') . '</li>';
print '<li>' . mitms_captivate_popup('Setting up organisations', 'setting up organisations') . '</li>';
print '<li>' . mitms_captivate_popup('Setting up positions', 'setting up positions') . '</li>';
print '<li>' . mitms_captivate_popup('The gradebook', 'The gradebook') . '</li>';
print '<li>' . mitms_captivate_popup('Using course reports', 'using course reports') . '</li>';
print '</ul>';

print_footer();

