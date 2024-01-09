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
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('configuration', $p));
$PAGE->set_heading(get_string('configuration', $p));

#Define lib variable using the lib class
$lib = new lib();

#Output header HTML
echo $OUTPUT->header();

#Output link tag with the css
echo("<link rel='stylesheet' type='text/css' href='./classes/css/configuration.css'></link>");

#Use the configuration template to output HTML
$template = (object)[
    'roles_array' => array_values($lib->get_roles())
];
echo $OUTPUT->render_from_template('block_customnav/configuration', $template);

#Output script tag with the js
echo("<script src='./amd/min/configuration.min.js' defer></script>");
#Output footer HTML
echo $OUTPUT->footer();
#Log a event
\block_customnav\event\viewed_customnav_config::create(array('context' => \context_system::instance()))->trigger();