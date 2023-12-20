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
    $return['error'] = 'No ID provided.';
} else {
    $id = $_POST['id'];
    if(!preg_match("/^[0-9]*$/", $id) || empty($id)){
        #ID is not a number
        $return['error'] = 'Invalid ID provided.';
    } elseif($lib->check_role_id($id) != true){
        #Role with the id provided does not exist
        $return['error'] = 'Invalid ID provided';
    } elseif($lib->check_role_setting_exists($id) != true){
        #Role settings doesn't exist
        $return['error'] = 'Settings for the role does not exist, please create the settings.';
    } else {
        #Proceed if the validation has passed
        $settings = $lib->get_role_settings($id);
        $aspect = ($settings[2] == 1) ? 'object-fit:contain;' : '';
        #Generate HTML dependant on the data stored in the images and settings table for the role id provided
        #object-fit:contain;
        if($lib->check_role_images_exists($id) != true){
            #Create starter HTML if there isn't any images already in the customnav_images table
            $return['success'] = "
                <div class='cn-image-div-inner'>
                    <span class='c-pointer' onclick='cn_remove_icon(0)'><b>X</b></span>
                    <h4 class='text-center'>1</h4>
                    <p>URL: <input class='cn-rd-form-url' type='text' required></p>
                    <img class='cn-rd-form-img' style='width:$settings[0]px;height:$settings[1]px;$aspect' src=''>
                    <p>Image: <input class='cn-rd-form-image' type='file' onchange='cn_new_file(0)'></p>
                    <p>Text: <input class='cn-rd-form-text' type='text'></p>
                    <p>Alt text: <input class='cn-rd-form-alttext' type='text'></p>
                </div>
            ";
        } else {
            #Create HTML dependant on how many image records are available for the current role id provided
            $array = $lib->get_role_images_roleid($id);
            foreach($array as $arr){
                $return['success'] .= "
                <div class='cn-image-div-inner'>
                    <span class='c-pointer' onclick='cn_remove_icon(".($arr[0]-1).")'><b>X</b></span>
                    <h4 class='text-center'>$arr[0]</h4>
                    <p>URL: <input class='cn-rd-form-url' type='text' required value='$arr[1]'></p>
                    <img class='cn-rd-form-img' style='width:$settings[0]px;height:$settings[1]px;$aspect' src='$arr[2]'>
                    <p>Image: <input class='cn-rd-form-image' type='file' onchange='cn_new_file(".($arr[0]-1).")'></p>
                    <p>Text: <input class='cn-rd-form-text' type='text' value='$arr[3]'></p>
                    <p>Alt text: <input class='cn-rd-form-alttext' type='text' value='$arr[4]'></p>
                </div>
                ";
            }
        }
        $return['success'] = str_replace("  ","",$return['success']);
        $_SESSION['cn_rd_form_id'] = $id;
    }
}
echo(json_encode($return));