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
#Validate whether the required paramaters are set
if(!isset($_SESSION['cn_rs_form_id']) || !isset($_POST['width']) || !isset($_POST['height']) || !isset($_POST['aspect']) || !isset($_POST['icons'])){
    $return['error'] = get_string('missing_rv', 'block_customnav');
}else {
    #Set variables
    $id = $_SESSION['cn_rs_form_id'];
    $width = $_POST['width'];
    $height = $_POST['height'];
    $aspect = $_POST['aspect'];
    $icons = $_POST['icons'];
    #Validation
    if(!preg_match("/^[0-9]*$/", $id) || empty($id)){
        #ID is not a number
        $return['error'] = get_string('invalid_id_p', 'block_customnav');
    } elseif($lib->check_role_id($id) != true){
        #Role with the id provided does not exist
        $return['error'] = get_string('invalid_id_p', 'block_customnav');
    } elseif(!preg_match("/^[0-9]*$/", $width) || empty($width)){
        #Invalid with provided
        $return['error'] = get_string('invalid_wp', 'block_customnav');
    } elseif(!preg_match("/^[0-9]*$/", $height) || empty($height)){
        #Invalid height provided
        $return['error'] = get_string('invalid_hp', 'block_customnav');
    } elseif($aspect != 1 && $aspect != 0){
        #Invalid aspect ratio provided
        $return['error'] = get_string('invalid_ar', 'block_customnav');
    } elseif(!preg_match("/^[0-9]*$/", $icons) || empty($icons)){
        #Invalid icons per row provided
        $return['error'] = get_string('invalid_iprp', 'block_customnav');
    } elseif($width < 10){
        #Width is less than 10
        $return['error'] = get_string('width_mbg', 'block_customnav');
    } elseif($height < 10){
        #Height is less than 10
        $return['error'] = get_string('height_mbg', 'block_customnav');
    } elseif($icons < 1){
        #Icons per row is less than 1
        $return['error'] = get_string('icons_mbg', 'block_customnav');
    } else {
        #Proceed if validation has passed
        $return['success'] = ($lib->set_role_settings($id, $width, $height, $aspect, $icons)) ? true : false;
    }
}

#Output return as json
echo(json_encode($return));