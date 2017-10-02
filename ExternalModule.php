<?php
/**
 * @file
 * Provides ExternalModule class for Linear Data Entry Workflow.
 */

namespace EnrollIntoArm\ExternalModule;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;

/**
 * ExternalModule class for Linear Data Entry Workflow.
 */
class ExternalModule extends AbstractExternalModule {

    /**
     * @inheritdoc
     */
    function hook_every_page_top($project_id) {

    }

    /**
     * @inheritdoc
     */
    function hook_data_entry_form($project_id, $record, $instrument, $event_id, $group_id) {
        include_once 'includes/enroll_helper.php';
        
        $project_settings = json_decode($this->getProjectSetting("enroll-into-arm-module-settings", $project_id));
        echo var_dump($project_settings->randomization_field);
        enroll_into_arm_enroll_helper($project_id, $project_settings);
    }
}
