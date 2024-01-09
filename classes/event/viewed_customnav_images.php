<?php
/**
 * @package   block_customnav
 * @author    Robert Tyrone Cullen
 */
defined('MOODLE_INTERNAL') || die();
namespace block_customnav\event;
use core\event\base;

class viewed_customnav_images extends base{
    protected function init(){
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'customnav_images';
    }
    public static function get_name(){
        return 'Viewed images record(s)';
    }
    public function get_description(){
        return "The user with id '".$this->userid."' viewed record(s) for the role with id '".$this->other."'.";
    }
    public function get_url(){
        return new \moodle_url('/blocks/customnav/configuration.php');
    }
    public function get_id(){
        return $this->objectid;
    }
}