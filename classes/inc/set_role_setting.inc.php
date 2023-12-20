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
    $return['error'] = 'Missing required value(s).';
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
        $return['error'] = 'Required value is not number.';
    } elseif($lib->check_role_id($id) != true){
        #Role with the id provided does not exist
        $return['error'] = 'Invalid required value provided';
    } elseif(!preg_match("/^[0-9]*$/", $width) || empty($width)){
        #Invalid with provided
        $return['error'] = 'Invalid width provided.';
    } elseif(!preg_match("/^[0-9]*$/", $height) || empty($height)){
        #Invalid height provided
        $return['error'] = 'Invalid height provided.';
    } elseif($aspect != 1 && $aspect != 0){
        #Invalid aspect ratio provided
        $return['error'] = 'Invalid aspect ratio.';
    } elseif(!preg_match("/^[0-9]*$/", $icons) || empty($icons)){
        #Invalid icons per row provided
        $return['error'] = 'Invalid icons per row provided.';
    } elseif($width < 10){
        #Width is less than 10
        $return['error'] = 'Width must be 10 or greater.';
    } elseif($height < 10){
        #Height is less than 10
        $return['error'] = 'Height must be 10 or greater.';
    } elseif($icons < 1){
        #Icons per row is less than 1
        $return['error'] = 'Icons per row must be 1 or greater.';
    } else {
        #Proceed if validation has passed
        $return['success'] = ($lib->set_role_settings($id, $width, $height, $aspect, $icons)) ? true : false;
    }
}

#Output return as json
echo(json_encode($return));