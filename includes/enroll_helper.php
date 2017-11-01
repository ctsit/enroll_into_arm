<?php
/**
 * @file
 * Enrols a person into an arm.
 */

/**
 * This function is used to insert the record_id and subject_id into the randomly selected arm.
 */
function enroll_into_arm_enroll_helper($project_id, $project_settings, $record_id, $event_id) {
    global $Proj;
    if (!enroll_into_arm_rand_group_exists($Proj->metadata, $project_settings['randomization_field'])) {
        return;
    }
    if (empty($record_id)) {
        return;
    }

    if (!($rad_val = enroll_into_arm_get_field_data($project_id, $record_id, $event_id, $project_settings['randomization_field']))) {
        return;
    }
    if (!($arm_name = enroll_into_arm_get_arm_name($project_settings['randomization_mappings'], $rad_val))) {
        return;
    }

    if (!($next_event_id = enroll_into_arm_get_affiliated_event($arm_name))) {
        return;
    }

    $first_name = enroll_into_arm_get_field_data($project_id, $record_id, $event_id, $project_settings['first_name']);
    $last_name = enroll_into_arm_get_field_data($project_id, $record_id, $event_id, $project_settings['last_name']);

    $subject_id = enroll_into_arm_format_subject_id($record_id, $first_name, $last_name, $project_settings['pad_digits']);

    // save record_id and subject_id into the randomly selecetd arm
    enroll_into_arm_save_record_field($project_id, $next_event_id, $record_id, "record_id", $record_id);
    enroll_into_arm_save_record_field($project_id, $next_event_id, $record_id, $project_settings['subject_id'], $subject_id);
}

/*
* This function format the subject id to the following format.
* dag_id + "-" + first_name_initial + last_name_initial + user_portion
* User portion is the 0 padded record_id.
*/
function enroll_into_arm_format_subject_id($record_id, $firstname, $lastname, $padding_digits) {
    $rec_arr = explode("-", $record_id);
    $dag_id = "";
    $s_record_id = "";
    if (count($rec_arr) == 2) {
        $dag_id = $rec_arr[0];
        $s_record_id = $rec_arr[1];
    } else {
        $s_record_id = $rec_arr[0];
    }
    if (!($firstname != null && strlen($firstname) > 0 && $lastname != null && strlen($lastname) > 0)) {
        return false;
    }
    
    //do padding.
    $padded_s_record_id = str_pad($s_record_id, $padding_digits, "0", STR_PAD_LEFT);
    $res = substr($firstname, 0, 1) . substr($lastname, 0, 1) . $padded_s_record_id;
    if (count($rec_arr) == 2) {
        $res = $dag_id . '-' . $res;
    }
    return $res;
}

/*
* This function get the arm name from the projects_settings and the $random_value that is selected.
* return arm_name if doesn't get anything returns false instead.
*/
function enroll_into_arm_get_arm_name($randomization_mappings, $random_value) {
    foreach($randomization_mappings as $arm_details) {
        if ($arm_details['value'] == $random_value) {
            return $arm_details['arm_to_enroll'];
        }
    }
    return false;
}

/*
* This method is used get the field_value of a field.
* Return "" if it can get nothing.
*/
function enroll_into_arm_get_field_data($project_id, $record_id, $event_id, $field_name) {
    $data = REDCap::getData($project_id, 'array', $record_id, $field_name, $event_id);
    if (empty($data)) {
        return "";
    }
    return $data[$record_id][$event_id][$field_name];
}

/*
* This method returns the event_id that is affiliated with the given arm_name.
* returns false if it can get nothing.
*/
function enroll_into_arm_get_affiliated_event($arm) {
    $events = REDCap::getEventNames(true, false);
    foreach($events as $event_id => $details) {
        if (strpos($details, $arm) !== false) {
            return $event_id;
        }
    }
    return false;
}

/*
* This functions check if a field_name exists in the instrument or not.
* If present it return true or else false;
*/
function enroll_into_arm_rand_group_exists($project_metadata, $field_name) {
    foreach ($project_metadata as $field => $info) {
        if (strcmp($field, $field_name) == 0) {
            return true;
        }
    }
    return false;
}

/*
* This function inserts or updated into redcap_data table.
* If successful it returns true.
*/
function enroll_into_arm_save_record_field($project_id, $event_id, $record_id, $field_name, $value, $instance = null) {
    $readsql = "SELECT 1 from redcap_data where project_id = $project_id and event_id = $event_id and record = '".db_escape($record_id)."' and field_name = '".db_escape($field_name)."' " . ($instance == null ? "AND instance is null" : "AND instance = '".db_escape($instance)."'");
    $q = db_query($readsql);
    if (!$q) return false;
    $record_count = db_result($q, 0);
    if ($record_count == 0) {
        if (isSet($instance)) {
            $sql = "INSERT INTO redcap_data (project_id, event_id, record, field_name, value, instance) " . "VALUES ($project_id, $event_id, '".db_escape($record_id)."', '".db_escape($field_name)."', '".db_escape($value)."' , $instance)";
        } else {
            $sql = "INSERT INTO redcap_data (project_id, event_id, record, field_name, value) " . "VALUES ($project_id, $event_id, '".db_escape($record_id)."', '".db_escape($field_name)."', '".db_escape($value)."')";
        }
        $q = db_query($sql);
        if (!$q) return false;
        return true;
    } else {
        $sql = "UPDATE redcap_data set value = '".db_escape($value)."' where project_id = $project_id and event_id = $event_id and record = '".db_escape($record_id)."' and field_name = '".db_escape($field_name)."' " . ($instance == null ? "AND instance is null" : "AND instance = '".db_escape($instance)."'");
        $q = db_query($sql);
        if (!$q) return false;
        return true;
    }
}
