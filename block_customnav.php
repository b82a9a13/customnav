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
                $this->title = get_string('manager_n', 'block_customnav');
            } elseif($role == 'editingteacher'){
                $this->title = get_string('editing_cn', 'block_customnav');
            } elseif($role == 'teacher'){
                $this->title = get_string('coach_n', 'block_customnav');
            } elseif($role == 'student'){
                $this->title = get_string('learner_n', 'block_customnav');
            } else{
                $this->title = get_string('failed_title', 'block_customnav');
            }
        } else {
            $this->title = get_string('custom_n', 'block_customnav');
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
                #Add hyperlink to the configuration page for the plugin
                $this->content->text = "<div class='text-center'><a href='./../blocks/customnav/configuration.php'>".get_string('custom_nc', 'block_customnav')."</a></div>";
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
                            $this->content->text .= "<a href='$arr[1]' class='mr-1 ml-1'><img style='$style' src='$arr[2]' alt='$arr[4]'></a>";
                        } elseif($arr[3] != null){
                            $this->content->text .= "<a href='$arr[1]' class='mr-1 ml-1'>$arr[3]</a>";
                        }
                        $pos++;
                    }
                    $this->content->text .= "</div>";
                    \block_customnav\event\viewed_customnav_manager::create(array('context' => \context_system::instance(), 'other' => $role))->trigger();
                }
            } elseif($role == 'editingteacher'){
                #Require the relevant capability and ensure the user has the relevant role
                $courseid = $lib->get_archetype_courseid($role);
                if($courseid != 0){
                    if(has_capability('block/customnav:coach', context_course::instance($courseid))){
                        require_capability('block/customnav:coach', context_course::instance($courseid));
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
                                    $this->content->text .= "<a href='$arr[1]' class='mr-1 ml-1'><img style='$style' src='$arr[2]' alt='$arr[4]'></a>";
                                } elseif($arr[3] != null){
                                    $this->content->text .= "<a href='$arr[1]' class='mr-1 ml-1'>$arr[3]</a>";
                                }
                                $pos++;
                            }
                            $this->content->text .= "</div>";
                            \block_customnav\event\viewed_customnav_editingteacher::create(array('context' => \context_course::instance($courseid), 'other' => $role))->trigger();
                        }
                    }
                }
            } elseif($role == 'teacher'){
                #Require the relevant capability and ensure the user has the relevant role
                $courseid = $lib->get_archetype_courseid($role);
                if($courseid != 0){
                    if(has_capability('block/customnav:coach', context_course::instance($courseid))){
                        require_capability('block/customnav:coach', context_course::instance($courseid));
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
                                    $this->content->text .= "<a href='$arr[1]' class='mr-1 ml-1'><img style='$style' src='$arr[2]' alt='$arr[4]'></a>";
                                } elseif($arr[3] != null){
                                    $this->content->text .= "<a href='$arr[1]' class='mr-1 ml-1'>$arr[3]</a>";
                                }
                                $pos++;
                            }
                            $this->content->text .= "</div>";
                            \block_customnav\event\viewed_customnav_teacher::create(array('context' => \context_course::instance($courseid), 'other' => $role))->trigger();
                        }
                    }
                }
            } elseif($role == 'student'){
                #Require the relevant capability and ensure the user has the relevant role
                $courseid = $lib->get_archetype_courseid($role);
                if($courseid != 0){
                    if(has_capability('block/customnav:learner', context_course::instance($courseid))){
                        require_capability('block/customnav:learner', context_course::instance($courseid));
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
                                    $this->content->text .= "<a href='$arr[1]' class='mr-1 ml-1'><img style='$style' src='$arr[2]' alt='$arr[4]'></a>";
                                } elseif($arr[3] != null){
                                    $this->content->text .= "<a href='$arr[1]' class='mr-1 ml-1'>$arr[3]</a>";
                                }
                                $pos++;
                            }
                            $this->content->text .= "</div>";
                            \block_customnav\event\viewed_customnav_student::create(array('context' => \context_course::instance($courseid), 'other' => $role))->trigger();
                        }
                    }
                }
            }
        }
    }
}