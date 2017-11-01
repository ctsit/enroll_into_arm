# Enroll into Arm

A REDCap Module to control the enrollment of subject into a study arm based on the value of a categorical variable. This feature allows automatic enrollment immediately after randomization with the [REDCap randomization module](https://apps.icts.uiowa.edu/confluence/display/REDCapDocs/REDCap+Randomization+Module).

This feature also defaults subject_id in the affiliated with the given format.
<DAG_ID> + "-" + <FIRST_LETTER_IN_FIRST_NAME> + <FIRST_LETTER_IN_LAST_NAME> + <RECORD_ID_WITH_0_PADDED_DIGITS>

## Prerequisites
- [REDCap Modules](https://github.com/vanderbilt/redcap-external-modules)

## Installation
- Clone this repo into to an `<redcap-root>/modules/enroll_into_arm_v1.0`
- Go to **Control Center > Manage External Modules** and enable Enroll into arm module.
- From the project you want to use this module, access **Manage External Module** page and enable Enroll Into Arm.

## Configuration
Yet on **Manage External Module**, click on **Configure** button in order to fill the settings form. Here is an example of use (import `sample.xml` to have a project that fits the example below):

* **Randomization field**: `rand_options` (let's say that `rand_options` is a randomized dropdown field - located in the first Arm of your project - whose options are `2` and `3`, referencing arms 2 and 3)
* **Randomization field mappings**:
  1. Value: `2` / Destination arm: `arm_2` (if value is randomly set as `2`, the subject will be enrolled into `arm_2` arm)
  2. Value: `3` / Destination arm: `arm_3` (if value is randomly set as `3`, the subject will be enrolled into `arm_2` arm)
* **PAD digits length**: `3` (used to build subject ID value)
* **First name field**: `first_name` (used to build subject ID value)
* **Last name field**: `last_name` (used to build subject ID value)
* **Subject ID field**: `subject_id` (the destination field to receive the formatted subject ID value)
