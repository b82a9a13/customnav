<?php
/**
 * @package   block_customnav
 * @author    Robert Tyrone Cullen
 */
defined('MOODLE_INTERNAL') || die();
namespace block_customnav\event;
use core\event\base;

class updated_customnav_images extends base{
    protected function init(){
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'customnav_images';
    }
    public static function get_name(){
        return 'Updated a image record';
    }
    public function get_description(){
        return "The user with id '".$this->userid."' updated a record for the role with id '".$this->other[0]."' and for the position '".$this->other[1]."'.";
    }
    public function get_url(){
        return new \moodle_url('/blocks/customnav/configuration.php');
    }
    public function get_id(){
        return $this->objectid;
    }
}