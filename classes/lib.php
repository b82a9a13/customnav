<?php
/**
 * @package     block_customnav
 * @author      Robert Tyrone Cullen
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */
namespace block_customnav;
use stdClass;

class lib{
    #Get all the relevant roles
    function get_roles(): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT shortname, id FROM {role} WHERE archetype = "manager" OR archetype = "teacher" OR archetype = "editingteacher" OR archetype = "student"');
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->shortname, $record->id]);
        }
        asort($array);
        return $array;
    }

    #Check if the role id provided is valid
    function check_role_id(int $id): bool{
        global $DB;
        return ($DB->get_record_sql('SELECT * FROM {role} WHERE (archetype = "manager" OR archetype = "teacher" OR archetype = "editingteacher" OR archetype = "student") AND id = ?',[$id])->id != null) ? true : false;
    }
    
    #Check if the role id provided has settings stored in the database already
    function check_role_setting_exists(int $id): bool{
        global $DB;
        return ($DB->record_exists('customnav_settings', [$DB->sql_compare_text('roleid') => $id]) == true) ? true : false;
    }

    #Get the short name for a specific role id
    function get_roleid_shortname(int $id): string{
        global $DB;
        return $DB->get_record_sql('SELECT shortname FROM {role} WHERE id = ?',[$id])->shortname;
    }

    #Get the id of the record for a specific role id
    function get_role_settings_id(int $id): int{
        global $DB;
        return $DB->get_record_sql('SELECT id FROM {customnav_settings} WHERE roleid = ?',[$id])->id;
    }

    #Set the settings for a specific role id
    function set_role_settings(int $id, int $width, int $height, int $aspect, int $icons): bool{
        global $DB;
        #Create a new record class
        $record = new stdClass();
        $record->roleid = $id;
        $record->width = $width;
        $record->height = $height;
        $record->aspectratio = $aspect;
        $record->iconsperrow = $icons;
        if(!$DB->record_exists('customnav_settings', [$DB->sql_compare_text('roleid') => $id])){
            #Insert a record into the table
            return ($DB->insert_record('customnav_settings', $record)) ? true : false;
        } else {
            #Add the id to the record class and update the record
            $record->id = $this->get_role_settings_id($id);
            return ($DB->update_record('customnav_settings', $record)) ? true : false;
        }
    }

    #Get the settings for a specific role id
    function get_role_settings(int $id): array{
        global $DB;
        $record = $DB->get_record_sql('SELECT * FROM {customnav_settings} WHERE roleid = ?',[$id]);
        return [$record->width, $record->height, $record->aspectratio, $record->iconsperrow];
    }

    #Check if images for the role id exists
    function check_role_images_exists(int $id): bool{
        global $DB;
        return ($DB->record_exists('customnav_images', [$DB->sql_compare_text('settingid') => $this->get_role_settings_id($id)]) == true) ? true : false;
    }

    #Get a specific id of a record for a specific role and position
    function get_role_images_id(int $id, int $pos): int{
        global $DB;
        return $DB->get_record_sql('SELECT id FROM {customnav_images} WHERE settingid = ? and position = ?',[$id, $pos])->id;
    }

    #Function is called to create/update records for a speific role id images
    function set_role_images(int $id, array $data): bool{
        global $DB;
        $settingid = $this->get_role_settings_id($id);
        foreach($data as $dat){
            $record = new stdClass();
            $record->position = $dat[0];
            $record->url = $dat[2];
            if($dat[1] == true){
                $record->image = $dat[3];
                $record->text = null;
            } elseif($dat[1] == false){
                $record->text = $dat[3];
                $record->image = null;
            }
            $record->alttext = $dat[4];
            $record->settingid = $settingid;
            if($DB->record_exists('customnav_images', [$DB->sql_compare_text('settingid') => $settingid, $DB->sql_compare_text('position') => $dat[0]])){
                #Update record
                $record->id = $this->get_role_images_id($settingid, $dat[0]);
                if($DB->update_record('customnav_images', $record) == false){
                    return false;
                }
            } else {
                #Create new record
                if($DB->insert_record('customnav_images', $record) == false){
                    return false;
                }
            }
        }
        return true;
    }

    #Function is called to get all images for a specific role id
    function get_role_images_roleid(int $id): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT * FROM {customnav_images} WHERE settingid = ?',[$this->get_role_settings_id($id)]);
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->position, $record->url, $record->image, $record->text, $record->alttext]);
        }
        return $array;
    }

    #Function is called to remove a specific icon with a specified position and role id
    function remove_role_images(int $roleid, int $pos): bool{
        global $DB;
        $settingid = $this->get_role_settings_id($roleid);
        if(!$DB->record_exists('customnav_images', [$DB->sql_compare_text('settingid') => $settingid, $DB->sql_compare_text('position') => $pos])){
            #Return true if the record does not exist
            return true;
        } else{
            #Delete the selected record
            $DB->delete_records('customnav_images', [$DB->sql_compare_text('settingid') => $settingid, $DB->sql_compare_text('position') => $pos]);
            #Get all the records for the specified role id
            $records = $DB->get_records_sql('SELECT * FROM {customnav_images} WHERE settingid = ? ORDER BY position',[$settingid]);
            #Loop through the records and update the position for the records
            $pos = 1;
            foreach($records as $record){
                $update = new stdClass();
                $update->id = $record->id;
                $update->position = $pos;
                if($DB->update_record('customnav_images', $update) == false){
                    #If a update fails return false
                    return false;
                }
                $pos++;
            }
        }
        return true;
    }
}