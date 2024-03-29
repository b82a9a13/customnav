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
    #No ID provided
    $return['error'] = get_string('no_id_p', 'block_customnav');
} else {
    $id = $_POST['id'];
    if(!preg_match("/^[0-9]*$/", $id) || empty($id)){
        #ID is not a number
        $return['error'] = get_string('invalid_id_p', 'block_customnav');
    } elseif($lib->check_role_id($id) != true){
        #Role with the id provided does not exist
        $return['error'] = get_string('invalid_id_p', 'block_customnav');
    } elseif($lib->check_role_setting_exists($id) != true){
        #Role settings doesn't exist
        $return['error'] = get_string('setting_ne', 'block_customnav');
    } else {
        #Proceed if the validation has passed
        $settings = $lib->get_role_settings($id);
        $aspect = ($settings[2] == 1) ? 'object-fit:contain;' : '';
        #Generate HTML dependant on the data stored in the images and settings table for the role id provided
        #object-fit:contain;
        if($lib->check_role_images_exists($id) != true){
            #Create starter HTML if there isn't any images already in the customnav_images table
            $return['success'] = "
                <div class='cn-image-div-inner border'>
                    <span class='c-pointer' onclick='cn_remove_icon(0)'><b>X</b></span>
                    <h4 class='text-center'>1</h4>
                    <p>URL: <input class='cn-rd-form-url w-75' type='text' required></p>
                    <p><span class='c-pointer' onclick='cn_remove_img(0)'><b>X</b></span></p>
                    <img class='cn-rd-form-img' style='width:$settings[0]px;height:$settings[1]px;$aspect' src=''>
                    <p>Image: <input class='cn-rd-form-image' type='file' onchange='cn_new_file(0)'></p>
                    <p>Text: <input class='cn-rd-form-text w-75' type='text'></p>
                    <p>Alt text: <input class='cn-rd-form-alttext w-75' type='text'></p>
                </div>
            ";
        } else {
            #Create HTML dependant on how many image records are available for the current role id provided
            $array = $lib->get_role_images_roleid($id);
            $return['success'] = "";
            foreach($array as $arr){
                $return['success'] .= "
                <div class='cn-image-div-inner border'>
                    <span class='c-pointer' onclick='cn_remove_icon(".($arr[0]-1).")'><b>X</b></span>
                    <h4 class='text-center'>$arr[0]</h4>
                    <p>URL: <input class='cn-rd-form-url w-75' type='text' required value='$arr[1]'></p>
                    <p><span class='c-pointer' onclick='cn_remove_img(".($arr[0]-1).")'><b>X</b></span></p>
                    <img class='cn-rd-form-img' style='width:$settings[0]px;height:$settings[1]px;$aspect' src='$arr[2]'>
                    <p>Image: <input class='cn-rd-form-image' type='file' onchange='cn_new_file(".($arr[0]-1).")'></p>
                    <p>Text: <input class='cn-rd-form-text w-75' type='text' value='$arr[3]'></p>
                    <p>Alt text: <input class='cn-rd-form-alttext w-75' type='text' value='$arr[4]'></p>
                </div>
                ";
            }
            \block_customnav\event\viewed_customnav_images::create(array('context' => \context_system::instance(), 'other' => $id))->trigger();
        }
        $return['success'] = str_replace("\n","", str_replace("\r","", str_replace("  ","",$return['success'])));
        $_SESSION['cn_rd_form_id'] = $id;
    }
}
echo(json_encode($return));