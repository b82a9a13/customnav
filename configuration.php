<?php
require_once(__DIR__ . '/../../config.php');
#Require the user to be logged in
require_login();

#Require that the user has the role of administrator
$context = context_system::instance();
require_capability('block/customnav:admin', $context);

#Include lib class
use block_customnav\lib;

#Set $p for the strings
$p = 'block_customnav';

#Set required $PAGE paramters
$PAGE->set_url(new moodle_url('/blocks/customnav/configuration.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('configuration', $p));
$PAGE->set_heading(get_string('configuration', $p));
$PAGE->set_pagelayout('admin');

#Define lib variable using the lib class
$lib = new lib();

#Output header HTML
echo $OUTPUT->header();

#Output link tag with the css
echo("<link rel='stylesheet' type='text/css' href='./classes/css/configuration.css'></link>");

#Use the configuration template to output HTML
$template = (object)[
    'roles_array' => array_values($lib->get_roles()),
    'txt_settings' => get_string('settings', $p),
    'txt_displays' => get_string('displays', $p),
    'txt_width' => get_string('width', $p),
    'txt_height' => get_string('height', $p),
    'txt_keep_ar' => get_string('keep_ar', $p),
    'txt_icons_pr' => get_string('icons_pr', $p),
    'txt_submit' => get_string('submit', $p),
    'txt_role' => get_string('role', $p),
    'txt_add_ni' => get_string('add_ni', $p)
];
echo $OUTPUT->render_from_template("$p/configuration", $template);

#Output script tag with the js
echo("<script src='./amd/min/configuration.min.js' defer></script>");
#Output footer HTML
echo $OUTPUT->footer();
#Log a event
\block_customnav\event\viewed_customnav_config::create(array('context' => \context_system::instance()))->trigger();