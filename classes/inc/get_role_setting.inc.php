<?php
#include config file
require_once(__DIR__.'/../../../../config.php');
#Require the user to be logged in
require_login();
#Require that the user has the role of administrator
$context = context_system::instance();
require_capability('block/customnav:admin', $context);
#Create a variable from the lib class
use block_customnav\lib;
$lib = new lib;

#Create error text variable
$return = [];
#Handle the post data and send reponses dependant on the data recieved
if(!isset($_POST['id'])){
    $return['error'] = get_string('no_id_p', 'block_customnav');
} else {
    $id = $_POST['id'];
    if(!preg_match("/^[0-9]*$/", $id) || empty($id)){
        #ID is not a number
        $return['error'] = get_string('invalid_id_p', 'block_customnav');
    } elseif($lib->check_role_id($id) != true){
        #Role with the id provided does not exist
        $return['error'] = get_string('invalid_id_p', 'block_customnav');
    } else {
        if($lib->check_role_setting_exists($id) != true){
            #Return new as true and the name of the role
            $name = $lib->get_roleid_shortname($id);
            $return['success'] = new stdClass();
            $return['success']->name = $name;
            $return['success']->new = true;
        } else {
            #Return values for the form
            $return['success'] = new stdClass();
            $return['success']->name = $lib->get_roleid_shortname($id);
            $return['success']->new = false;
            $values = $lib->get_role_settings($id);
            $return['success']->width = $values[0];
            $return['success']->height = $values[1];
            $return['success']->aspect = ($values[2] == 1) ? true : false;
            $return['success']->icons = $values[3];
            \block_customnav\event\viewed_customnav_settings::create(array('context' => \context_system::instance(), 'other' => $id))->trigger();
        }
        #Set session cookie to the id of the current role setting
        $_SESSION['cn_rs_form_id'] = $id;
    }
}

echo(json_encode($return));