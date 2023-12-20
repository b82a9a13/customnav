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
if(!isset($_SESSION['cn_rd_form_id']) || !isset($_POST['total']) || !isset($_POST['url0']) || (!isset($_FILES['image0']) && !isset($_POST['image0'])) || !isset($_POST['text0']) || !isset($_POST['alttext0'])){
    $return['error'] = 'Missing required value(s)';
} else {
    #Validate the total parameter
    $total = $_POST['total'];
    if(!preg_match("/^[0-9]*$/", $total) || empty($total)){
        $return['error'] = 'Invalid total provided';
    } else{
        #Validate whether the required values are set for the total provided
        for($i = 0; $i < $total; $i++){
            if(!isset($_POST["url$i"]) || (!isset($_FILES["image$i"]) && !isset($_POST["image$i"])) || !isset($_POST["text$i"]) || !isset($_POST["alttext$i"])){
                $return['error'] = 'Missing required value(s) '.($i+1);
                break;
            } elseif(!filter_var($_POST["url$i"], FILTER_VALIDATE_URL) || empty($_POST["url$i"])){
                $return['error'] = "Invalid url ".($i+1);
                break;
            } elseif($_POST["image$i"] == 'undefined' && empty($_POST["text$i"])){
                $return['error'] = 'Image or text is required for a icon '.($i+1);
                break;
            } elseif(!preg_match("/^[0-9 a-zA-z]*$/", $_POST["text$i"]) && $_POST["image$i"] != 'undefined'){
                $return['error'] = 'Invalid text '.($i+1);
                break;
            } elseif(empty($_POST["text$i"]) && (end(explode(".", $_FILES["image$i"]["name"])) != 'png' && end(explode(".", $_FILES["image$i"]["name"])) != 'jpg' && end(explode(".", $_FILES["image$i"]["name"])) != 'jpeg' || $_FILES["image$i"]["type"] != "image/jpg" && $_FILES["image$i"]["type"] != 'image/png' && $_FILES["image$i"]["type"] != 'image/jpeg')){
                $return['error'] = 'Invalid file '.($i+1);
                break;
            } elseif($_POST["image$i"] != 'undefined' && !empty($_POST["text$i"])){
                $return['error'] = 'Please only use a image or text for '.($i+1).' and not both';
                break;
            }elseif(!preg_match("/^[0-9a-z A-Z]*$/", $_POST["alttext$i"]) || empty($_POST["alttext$i"])){
                $return['error'] = 'Invalid alt text '.($i+1);
                break;
            } else {
                //[Position, isImage, url, image/text, alttext]
                if($_POST["image$i"] != 'undefined'){
                    //Create base64 from image file
                    $file = $_FILES["image$i"];
                    $base64 = 'data:image/'.end(explode(".",$file['name'])).';base64,'.base64_encode(file_get_contents($file['tmp_name']));
                    //Add form data to an array
                    array_push($values, [$i+1, true, $_POST["url$i"], $base64, $_POST["alttext$i"]]);
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