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

#Create return variable
$return = [];
#Validate whether the required varaibles are set
if(!isset($_SESSION['cn_rd_form_id']) || !isset($_POST['id'])){
    #Set a 'error' for the return variable
    $return['error'] = 'Missing required value(s)';
} else {
    $id = $_POST['id'];
    if(!preg_match("/^[0-9]*$/", $id) || empty($id)){
        #Set a 'error' for the return variable
        $return['error'] = 'Invalid id provided';
    } else {
        #Set a 'success' for the return variable
        $return['success'] = ($lib->remove_role_images($_SESSION['cn_rd_form_id'], $id)) ? true : false;
    }
}
#Output the return variable as json
echo(json_encode($return));