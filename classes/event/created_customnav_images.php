<?php
/**
 * @package   block_customnav
 * @author    Robert Tyrone Cullen
 */
namespace block_customnav\event;
defined('MOODLE_INTERNAL') || die();
use core\event\base;

class created_customnav_images extends base{
    protected function init(){
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }
    public static function get_name(){
        return 'Created a new image record';
    }
    public function get_description(){
        return "The user with id '".$this->userid."' created a new record for the role with id '".$this->other[0]."' and for the position '".$this->other[1]."'.";
    }
    public function get_url(){
        return new \moodle_url('/blocks/customnav/configuration.php');
    }
    public function get_id(){
        return $this->objectid;
    }
}