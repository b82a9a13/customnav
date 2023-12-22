<?php
/**
 * @package   block_customnav
 * @author    Robert Tyrone Cullen
 */
//use the lib class
use block_customnav\lib;
class block_customnav extends block_base{
    //Initialization function, defines title
    public function init(){
        $lib = new lib();
        $role = $lib->get_current_role();
        if($role != ''){
            if($role == 'manager' && has_capability('block/customnav:admin', context_system::instance())){
                require_capability('block/customnav:admin', context_system::instance());
                $this->title = "Manager Navigation";
            } elseif($role == 'editingteacher'){
                $this->title = "Editing Coach Navigation";
            } elseif($role == 'teacher'){
                $this->title = "Coach Navigation";
            } elseif($role == 'student'){
                $this->title = "Learner Navigation";
            } else{
                $this->title = "{Failed to load role} Navigation";
            }
        } else {
            $this->title = 'Custom Navigation';
        }
    }
    //Content for the block
    public function get_content(){
        //Add a empty string to the content text
        $this->content = new stdClass();
        $this->content->text = '';
        //Create lib variable from the lib class
        $lib = new lib();
        $role = $lib->get_current_role();
        if($role != ''){
            #Check the user has the correct capability for the role in the $role variable
            if($role == 'manager' && has_capability('block/customnav:admin', context_system::instance())){
                #Requrie the relevant capability
                require_capability('block/customnav:admin', context_system::instance());
                #Get the data for the specified role archetype
                $array = $lib->get_archetype_content($role);
                #Determine if there is any data and create the html if there is any and output it to the block
                if($array != [] && $array[1] != [] && $array[0] != []){
                    $aspect = ($array[0][2] == 1) ? "object-fit:contain;" : "";
                    $style = "width:".$array[0][0]."px;height:".$array[0][1]."px;$aspect";
                    $this->content->text .= "<div class='text-center'>";
                    $pos = 0;
                    foreach($array[1] as $arr){
                        if($pos == $array[0][3]){
                            $pos = 0;
                            $this->content->text .= "</div><div class='text-center'>";
                        }
                        if($arr[2] != null){
                            $this->content->text .= "<a href='$arr[1]' class='mr-1 ml-1'><img style='$style' src='$arr[2]'></a>";
                        } elseif($arr[3] != null){
                            $this->content->text .= "<a href='$arr[1]' class='mr-1 ml-1'>$arr[3]</a>";
                        }
                        $pos++;
                    }
                    $this->content->text .= "</div>";
                }
            } elseif($role == 'editingteacher'){
                $this->content->text .= "Editing Coach Navigation";
            } elseif($role == 'teacher'){
                $this->content->text .= "Coach Navigation";
            } elseif($role == 'student'){
                $this->content->text .= "Learner Navigation";
            }
        }
    }
}