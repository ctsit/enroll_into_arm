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
        enroll_into_arm_enroll_helper($project_id, $this->getFormattedSettings($project_id), $record, $event_id);
    }

    /**
     * Formats settings into a hierarchical key-value pair array.
     *
     * @param int $project_id
     *   Enter a project ID to get project settings.
     *   Leave blank to get system settings.
     *
     * @return array $formmated
     *   The formatted settings.
     */
    function getFormattedSettings($project_id = null) {
        $config = $this->getConfig();

        if ($project_id) {
            $type = 'project';
            $settings = ExternalModules::getProjectSettingsAsArray($this->PREFIX, $project_id);
        }
        else {
            $type = 'system';
            $settings = ExternalModules::getSystemSettingsAsArray($this->PREFIX);
        }

        $formatted = array();
        foreach ($config[$type . '-settings'] as $field) {
            $key = $field['key'];

            if ($field['type'] == 'sub_settings') {
                // Handling sub settings.
                $formatted[$key] = array();

                if ($field['repeatable']) {
                    // Handling repeating sub settings.
                    foreach (array_keys($settings[$key]['value']) as $delta) {
                        foreach ($field['sub_settings'] as $sub_setting) {
                            $sub_key = $sub_setting['key'];
                            $formatted[$key][$delta][$sub_key] = $settings[$sub_key]['value'][$delta];
                        }
                    }
                }
                else {
                    foreach ($field['sub_settings'] as $sub_setting) {
                        $sub_key = $sub_setting['key'];
                        $formatted[$key][$sub_key] = reset($settings[$sub_key]['value']);
                    }
                }
            }
            else {
                $formatted[$key] = $settings[$key]['value'];
            }
        }

        return $formatted;
    }
}
