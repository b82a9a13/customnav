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
#Create values array to store form data
$values = [];
#Validate whether the requried value(s) are set
if(!isset($_SESSION['cn_rd_form_id']) || !isset($_POST['total']) || !isset($_POST['url0']) || !isset($_POST['img0']) || !isset($_POST['text0']) || !isset($_POST['alttext0'])){
    $return['error'] = get_string('missing_rv', 'block_customnav');
} else {
    #Validate the total parameter
    $total = $_POST['total'];
    if(!preg_match("/^[0-9]*$/", $total) || empty($total)){
        $return['error'] = get_string('invalid_tp', 'block_customnav');
    } else{
        #Validate whether the required values are set for the total provided, and validate the values
        for($i = 0; $i < $total; $i++){
            if(!isset($_POST["url$i"]) || !isset($_POST["img$i"]) || !isset($_POST["text$i"]) || !isset($_POST["alttext$i"])){
                $return['error'] = ($i+1).' = '.get_string('missing_rv', 'block_customnav');
                break;
            } elseif(!filter_var($_POST["url$i"], FILTER_VALIDATE_URL) || empty($_POST["url$i"])){
                $return['error'] = ($i+1).' = '.get_string('invalid_url', 'block_customnav');
                break;
            } elseif(empty($_POST["img$i"]) && empty($_POST["text$i"])){
                $return['error'] = ($i+1).' = '.get_string('image_otr', 'block_customnav');
                break;
            } elseif(!preg_match("/^[0-9 a-zA-z]*$/", $_POST["text$i"]) && empty($_POST["img$i"]) && !empty($_POST["text$i"])){
                $return['error'] = ($i+1).' = '.get_string('invalid_t', 'block_customnav').'. '.get_string('invalid_c', 'block_customnav').''.preg_replace("/[0-9 a-zA-Z]/", "", $_POST["text$i"]);
                break;
            } elseif(empty($_POST["text$i"]) && (!preg_match("/^[A-Za-z0-9\/:;,+=]*$/", $_POST["img$i"]) || !preg_match("/^data:image\/(png|jpeg);base64,/i", $_POST["img$i"])) && !empty($_POST["img$i"])){
                $return['error'] = ($i+1).' = '.get_string('invalid_i', 'block_customnav');
                break;
            } elseif(!empty($_POST["img$i"]) && !empty($_POST["text$i"])){
                $return['error'] = ($i+1).' = '.get_string('image_or_text', 'block_customnav');
                break;
            } elseif(!preg_match("/^[0-9a-z A-Z]*$/", $_POST["alttext$i"]) || empty($_POST["alttext$i"])){
                $return['error'] = ($i+1).' = '.get_string('invalid_at', 'block_customnav').'. '.get_string('invalid_c', 'block_customnav').''.preg_replace("/[0-9 a-zA-Z]/", "", $_POST["alttext$i"]);
                break;
            } elseif(strlen($_POST["img$i"]) > 500000){
                $return['error'] = ($i+1).' = '.get_string('image_tb', 'block_customnav');
                break;
            } elseif(strlen($_POST["text$i"]) > 100){
                $return['error'] = ($i+1).' = '.get_string('text_tb', 'block_customnav');
                break;
            } elseif(strlen($_POST["alttext$i"]) > 100){
                $return['error'] = ($i+1).' = '.get_string('alttext_tb', 'block_customnav');
                break;
            } elseif(strlen($_POST["url$i"]) > 4096){
                $return['error'] = ($i+1).' = '.get_string('url_tb', 'block_customnav');
                break;
            } else {
                //[Position, isImage, url, image/text, alttext]
                if(!empty($_POST["img$i"])){
                    //Add form data to an array
                    array_push($values, [$i+1, true, $_POST["url$i"], $_POST["img$i"], $_POST["alttext$i"]]);
                } else {
                    //Add form data to an array
                    array_push($values, [$i+1, false, $_POST["url$i"], $_POST["text$i"], $_POST["alttext$i"]]);
                }
            }
        }
        #Proceed if validation has passed
        if($return == [] && $values != []){
            $return['success'] = ($lib->set_role_images($_SESSION['cn_rd_form_id'], $values) == true) ? true : false;
        }
    }
}
#Output return as json
echo(json_encode($return));