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
        
        // get project settings from configuration data of the project.
        $project_settings = json_decode($this->getProjectSetting("enroll-into-arm-module-settings", $project_id));
        enroll_into_arm_enroll_helper($project_id, $project_settings, $record, $event_id);
    }
}
