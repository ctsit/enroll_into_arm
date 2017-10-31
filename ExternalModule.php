<?php
/**
 * @file
 * Provides ExternalModule class for Enroll into Arm.
 */

namespace EnrollIntoArm\ExternalModule;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;

/**
 * ExternalModule class for Enroll into Arm.
 */
class ExternalModule extends AbstractExternalModule {

    /**
     * @inheritdoc
     */
    function hook_save_record($project_id, $record, $instrument, $event_id, $group_id) {
        include_once 'includes/enroll_helper.php';
        
        // Get project settings from configuration data of the project.
        $output = ExternalModules::getProjectSettingsAsArray('enroll_into_arm', $project_id);
        $project_settings = array();

        // Create output as an array.
        foreach ($output as $key => $value) {
            $project_settings[$key] = $value['value'];
        }

        // Nesting elements under randomization_field_values.
        foreach ($project_settings['randomization_field_values'] as $key => $value) {
            $inner = array();
            $inner['value'] = $project_settings['value'][$key];
            $inner['arm_to_enroll'] = $project_settings['arm_to_enroll'][$key];
            $project_settings['randomization_field_values'][$key] = $inner;
        }

        unset($project_settings['value']);
        unset($project_settings['arm_to_enroll']);

        enroll_into_arm_enroll_helper($project_id, $project_settings, $record, $event_id);
    }
}
