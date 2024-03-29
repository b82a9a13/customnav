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
    public function get_roles(): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT shortname, id, archetype FROM {role} WHERE (archetype = "manager" OR archetype = "teacher" OR archetype = "editingteacher" OR archetype = "student") AND (shortname = "manager" OR shortname = "teacher" OR shortname = "editingteacher" OR shortname = "student")');
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->shortname, $record->id, $record->archetype]);
        }
        asort($array);
        return $array;
    }

    #Check if the role id provided is valid
    public function check_role_id(int $id): bool{
        global $DB;
        return ($DB->get_record_sql('SELECT * FROM {role} WHERE (archetype = "manager" OR archetype = "teacher" OR archetype = "editingteacher" OR archetype = "student") AND (shortname = "manager" OR shortname = "teacher" OR shortname = "editingteacher" OR shortname = "student") AND id = ? LIMIT 1',[$id])->id != null) ? true : false;
    }
    
    #Check if the role id provided has settings stored in the database already
    public function check_role_setting_exists(int $id): bool{
        global $DB;
        return ($DB->record_exists('customnav_settings', [$DB->sql_compare_text('roleid') => $id]) == true) ? true : false;
    }

    #Get the short name for a specific role id
    public function get_roleid_shortname(int $id): string{
        global $DB;
        return $DB->get_record_sql('SELECT shortname FROM {role} WHERE id = ? LIMIT 1',[$id])->shortname;
    }

    #Get the id of the record for a specific role id
    private function get_role_settings_id(int $id): int{
        global $DB;
        if($DB->record_exists('customnav_settings', [$DB->sql_compare_text('roleid') => $id])){
            return $DB->get_record_sql('SELECT id FROM {customnav_settings} WHERE roleid = ? LIMIT 1',[$id])->id;
        } else {
            return 0;
        }
    }

    #Set the settings for a specific role id
    public function set_role_settings(int $id, int $width, int $height, int $aspect, int $icons): bool{
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
            if($DB->insert_record('customnav_settings', $record)){
                \block_customnav\event\created_customnav_settings::create(array('context' => \context_system::instance(), 'other' => $id))->trigger();
                return true;
            } else {
                return false;
            }
        } else {
            #Add the id to the record class and update the record
            $record->id = $this->get_role_settings_id($id);
            if($DB->update_record('customnav_settings', $record)){
                \block_customnav\event\updated_customnav_settings::create(array('context' => \context_system::instance(), 'other' => $id))->trigger();
                return true;
            } else {
                return false;
            }
        }
    }

    #Get the settings for a specific role id
    public function get_role_settings(int $id): array{
        global $DB;
        $record = $DB->get_record_sql('SELECT * FROM {customnav_settings} WHERE roleid = ? LIMIT 1',[$id]);
        return [$record->width, $record->height, $record->aspectratio, $record->iconsperrow];
    }

    #Check if images for the role id exists
    public function check_role_images_exists(int $id): bool{
        global $DB;
        return ($DB->record_exists('customnav_images', [$DB->sql_compare_text('settingid') => $this->get_role_settings_id($id)]) == true) ? true : false;
    }

    #Get a specific id of a record for a specific role and position
    private function get_role_images_id(int $id, int $pos): int{
        global $DB;
        return $DB->get_record_sql('SELECT id FROM {customnav_images} WHERE settingid = ? and position = ? LIMIT 1',[$id, $pos])->id;
    }

    #Function is called to create/update records for a speific role id images
    public function set_role_images(int $id, array $data): bool{
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
                } else {
                    \block_customnav\event\updated_customnav_images::create(array('context' => \context_system::instance(), 'other' => [$id, $dat[0]]))->trigger();
                }
            } else {
                #Create new record
                if($DB->insert_record('customnav_images', $record) == false){
                    return false;
                } else {
                    \block_customnav\event\created_customnav_images::create(array('context' => \context_system::instance(), 'other' => [$id, $dat[0]]))->trigger();
                }
            }
        }
        return true;
    }

    #Function is called to get all images for a specific role id
    public function get_role_images_roleid(int $id): array{
        global $DB;
        $records = $DB->get_records_sql('SELECT * FROM {customnav_images} WHERE settingid = ?',[$this->get_role_settings_id($id)]);
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->position, $record->url, $record->image, $record->text, $record->alttext]);
        }
        asort($array);
        return $array;
    }

    #Function is called to remove a specific icon with a specified position and role id
    public function remove_role_images(int $roleid, int $pos): bool{
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
            \block_customnav\event\deleted_customnav_images::create(array('context' => \context_system::instance(), 'other' => [$roleid, $pos]))->trigger();
        }
        return true;
    }

    #Function is called to get the highest capaibility role the current user has
    public function get_current_role(): string{
        global $DB;
        global $USER;
        #Get the ID's for the accepted roles
        $types = $this->get_roles();
        #Get all roles assigned to the current user
        $records = $DB->get_records_sql('SELECT * FROM {role_assignments} WHERE userid = ?',[$USER->id]);
        #Create $role array to store unique roles which the user has
        $roles = [];
        foreach($records as $record){
            foreach($types as $type){
                if($record->roleid == $type[1]){
                    if(!in_array($type[2], $roles)){
                        array_push($roles, $type[2]);
                    }
                }
            }
        }
        #Return the highest level role the in the $roles array
        if(in_array('manager', $roles)){
            return 'manager';
        } elseif(in_array('editingteacher', $roles)){
            return 'editingteacher';
        } elseif(in_array('teacher', $roles)){
            return 'teacher';
        } elseif(in_array('student', $roles)){
            return 'student';
        } else {
            return '';
        }
    }

    #Function is called to get the role id for a specific archetype
    public function get_archetype_roleid(string $archetype): int{
        global $DB;
        if($archetype == 'manager' || $archetype == 'editingteacher' || $archetype == 'teacher' || $archetype == 'student'){
            return $DB->get_record_sql('SELECT id FROM {role} WHERE archetype = ? AND shortname = archetype LIMIT 1',[$archetype])->id;
        } else {
            return 0;
        }
    }

    #Function is called to get the custom navigation for a specific role archetype
    public function get_archetype_content(string $archetype): array{
        if($archetype == 'manager' || $archetype == 'editingteacher' || $archetype == 'teacher' || $archetype == 'student'){
            $roleid = $this->get_archetype_roleid($archetype);
            if($roleid != 0){
                if($this->check_role_setting_exists($roleid) && $this->check_role_images_exists($roleid)){
                    return [$this->get_role_settings($roleid), $this->get_role_images_roleid($roleid)];
                }
            }
        }
        return [];
    }

    #Get a course where the archetype provided is assigned to the current user
    public function get_archetype_courseid(string $archetype): int{
        global $DB;
        global $USER;
        if($archetype == 'editingteacher' || $archetype == 'teacher' || $archetype == 'student'){
            $roleid = $this->get_archetype_roleid($archetype);
            if($roleid != 0){
                $record = $DB->get_record_sql('SELECT ra.id as id, e.courseid as courseid FROM {user_enrolments} ue
                    INNER JOIN {enrol} e ON e.id = ue.enrolid
                    INNER JOIN {context} c ON c.instanceid = e.courseid
                    INNER JOIN {role_assignments} ra ON ra.contextid = c.id
                    WHERE ra.roleid = ? AND ue.userid = ? AND ue.status = 0 AND ra.userid = ue.userid LIMIT 1',
                [$roleid, $USER->id]);
                if($record != null){
                    return $record->courseid;
                }
            }
        }
        return 0;
    }
}