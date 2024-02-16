<?php
/**
 * @package   block_customnav
 * @author    Robert Tyrone Cullen
 */
namespace block_customnav\event;
defined('MOODLE_INTERNAL') || die();
use core\event\base;

class viewed_customnav_teacher extends base{
    protected function init(){
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }
    public static function get_name(){
        return 'Viewed teacher navigation';
    }
    public function get_description(){
        return "The user with id '".$this->userid."' viewed the navigation for the role archetype '".$this->other."'.";
    }
    public function get_url(){
        return new \moodle_url('/my/index.php');
    }
    public function get_id(){
        return $this->objectid;
    }
}