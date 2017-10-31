# Enroll into Arm

A REDCap Module to control the enrollment of subject into a study arm based on the value of a categorical variable. This feature allows automatic enrollment immediately after randomization with the REDCap randomization module.

This feature also defaults subject_id in the affiliated with the given format.
<DAG_ID> + "-" + <FIRST_LETTER_IN_FIRST_NAME> + <FIRST_LETTER_IN_LAST_NAME> + <RECORD_ID_WITH_0_PADDED_DIGITS>

## Prerequisites
- [REDCap Modules](https://github.com/vanderbilt/redcap-external-modules)

## Installation
- Clone this repo into to an `<redcap-root>/modules/enroll_into_arm_v1.0.0`
- Go to **Control Center > Manage External Modules** and enable Enroll into arm module.

## Configuration
- In the project go to Manage External Module and enable this module for the project.
- After enabling the module, hit the configure button and add the following json to `Enroll into arm module settings`. This will set the project_settings for that project.

```
Array
(
   [randomization_field] => "rand_group"
   [pad_digits] => 3
   [first_name] => "first_name"
   [last_name] => "last_name"
   [subject_id] => "subject_id"
   [randomization_field_values] => Array
      (
         [0] => Array
             (
                 [value] => "1"
                 [arm_to_enroll] => "baseline_arm_2"
             )

         [1] => Array
             (
                 [value] => "2"
                 [arm_to_enroll] => "baseline_arm_3"
             )

      )
    
)

```

## How to use?
- Enable the randomization for a field.
- The name of the field whose value is randomized should be placed under `project_settings->randomized_group`. 
- For construction of subject_id we need first_name, last_name and length of the padded digits to be given, so the field_names for these should be given under json, so that the module automatically picks these values.
- Also the field that stores the subject_id should be placed under `project_settings->subject_id`
