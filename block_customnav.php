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
        $this->title = 'Custom Navigation';
        
    }
    //Content for the block
    public function get_content(){
        //Add a empty string to the content text
        $this->content = new stdClass();
        $this->content->text = 'Custom Navigation Placeholder';
        //Create lib variable from the lib class
        $lib = new lib();

    }
}