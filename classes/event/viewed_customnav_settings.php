<?php
/**
 * @package   block_customnav
 * @author    Robert Tyrone Cullen
 */
namespace block_customnav\event;
defined('MOODLE_INTERNAL') || die();
use core\event\base;

class viewed_customnav_settings extends base{
    protected function init(){
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'customnav_settings';
    }
    public static function get_name(){
        return 'Viewed a setting record';
    }
    public function get_description(){
        return "The user with id '".$this->userid."' viewed a record for the role with id '".$this->other."'.";
    }
    public function get_url(){
        return new \moodle_url('/blocks/customnav/configuration.php');
    }
    public function get_id(){
        return $this->objectid;
    }
}