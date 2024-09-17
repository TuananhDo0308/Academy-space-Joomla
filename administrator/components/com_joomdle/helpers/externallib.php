<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Joomdle web services helper file
 *
 * @package    auth_joomdle
 * @copyright  2009 Qontori Pte Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Some definitions so we can reuse file from Moodle
define('PARAM_TEXT',  'text');
define('PARAM_INT',      'int');
define('PARAM_FLOAT',  'float');
define('PARAM_BOOL',     'bool');
define('PARAM_RAW', 'raw');
define('VALUE_DEFAULT', 0);
define('VALUE_OPTIONAL', 2);

class external_value {
}

class external_multiple_structure {
}

class external_single_structure {
}

class joomdle_helpers_external {

    public static function user_id_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'multilang compatible name, course unique'),
            )
        );
    }

    public static function list_courses_parameters() {
        return (
            array(
                'enrollable_only' => new external_value(PARAM_INT, 'Return only enrollable courses'),
                'sortby' => new external_value(PARAM_TEXT, 'Order field'),
                'guest' => new external_value(PARAM_INT, 'Return only courses for guests'),
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function test_parameters() {
        return (
            array()
        );
    }

    public static function my_courses_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'Username'),
                'order_by_cat' => new external_value(PARAM_INT, 'order by category'),
            )
        );
    }

    public static function get_course_info_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_course_contents_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function courses_by_category_parameters() {
        return (
            array(
                'category' => new external_value(PARAM_INT, 'category id'),
                'enrollable_only' => new external_value(PARAM_INT, 'Return only enrollable courses'),
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_course_categories_parameters() {
        return (
            array(
                'category' => new external_value(PARAM_INT, 'category id'),
            )
        );
    }

    public static function get_course_editing_teachers_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_course_no_parameters() {
        return (
            array()
        );
    }

    public static function get_enrollable_course_no_parameters() {
        return (
            array()
        );
    }

    public static function get_student_no_parameters() {
        return (
            array()
        );
    }

    public static function get_total_assignment_submissions_parameters() {
        return (
            array()
        );
    }

    public static function get_course_students_no_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_assignment_submissions_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_assignment_grades_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_upcoming_events_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_news_items_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_user_grades_parameters() {
        return (
            array(
                'user' => new external_value(PARAM_TEXT, 'username'),
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_course_grade_categories_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_course_grade_categories_and_items_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function search_courses_parameters() {
        return (
            array(
                'text' => new external_value(PARAM_TEXT, 'text to search'),
                'phrase' => new external_value(PARAM_TEXT, 'search type'),
                'ordering' => new external_value(PARAM_TEXT, 'order'),
                'limit' => new external_value(PARAM_TEXT, 'limit'),
                'lang' => new external_value(PARAM_TEXT, 'lang'),
            )
        );
    }

    public static function search_categories_parameters() {
        return (
            array(
                'text' => new external_value(PARAM_TEXT, 'text to search'),
                'phrase' => new external_value(PARAM_TEXT, 'search type'),
                'ordering' => new external_value(PARAM_TEXT, 'order'),
                'limit' => new external_value(PARAM_TEXT, 'limit'),
                'lang' => new external_value(PARAM_TEXT, 'lang'),
            )
        );
    }

    public static function search_topics_parameters() {
        return (
            array(
                'text' => new external_value(PARAM_TEXT, 'text to search'),
                'phrase' => new external_value(PARAM_TEXT, 'search type'),
                'ordering' => new external_value(PARAM_TEXT, 'order'),
                'limit' => new external_value(PARAM_TEXT, 'limit'),
                'lang' => new external_value(PARAM_TEXT, 'lang'),
            )
        );
    }

    public static function get_my_courses_grades_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function check_moodle_users_parameters() {
        return (
                array(
                    'users' => new external_multiple_structure(
                      new external_single_structure(
                         array(
                            'username' => new external_value(PARAM_TEXT, 'username'),
                         )
                      )
                )
            )
        );
    }

    public static function get_moodle_only_users_parameters() {
        return (
            array(
                'users' => new external_multiple_structure(
                  new external_single_structure(
                     array(
                        'username' => new external_value(PARAM_TEXT, 'username'),
                     )
                  )
               ),
            'search' => new external_value(PARAM_TEXT, 'sarch text'),
            )
        );
    }

    public static function get_moodle_users_parameters() {
        return (
                        array(
                     'limitstart' => new external_value(PARAM_INT, 'limit start'),
                     'limit' => new external_value(PARAM_INT, 'limit'),
                     'order' => new external_value(PARAM_TEXT, 'order'),
                     'order_dir' => new external_value(PARAM_TEXT, 'order dir'),
                     'search' => new external_value(PARAM_TEXT, 'search text'),
                  )
            );
    }

    public static function get_moodle_users_number_parameters() {
        return (
            array(
                 'search' => new external_value(PARAM_TEXT, 'sarch text'),
              )
        );
    }

    public static function user_exists_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'multilang compatible name, course unique'),
            )
        );
    }

    public static function create_joomdle_user_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function create_joomdle_user_additional_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'app' => new external_value(PARAM_TEXT, 'app'),
            )
        );
    }

    public static function enrol_user_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'id' => new external_value(PARAM_INT, 'course id'),
                'roleid' => new external_value(PARAM_INT, 'role id'),
            )
        );
    }

    public static function multiple_enrol_and_addtogroup_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'courses' => new external_value(PARAM_TEXT, 'course shortnames'),
                'groups' => new external_value(PARAM_TEXT, 'group names'),
                'roleid' => new external_value(PARAM_INT, 'role id'),
            )
        );
    }

    public static function multiple_enrol_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'courses' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'id' => new external_value(PARAM_INT, 'course id'),
                                        )
                                )
                            ),
                'roleid' => new external_value(PARAM_INT, 'role id'),
            )
        );
    }

    public static function user_details_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function user_details_by_id_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'user id'),
            )
        );
    }

    public static function migrate_to_joomdle_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username')
            )
        );
    }

    public static function my_events_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                    array(
                       'id' => new external_value(PARAM_INT, 'course id'),
                        )
                    )
                )
            )
        );
    }

    public static function delete_user_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_mentees_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_roles_parameters() {
        return (
            array()
        );
    }

    public static function get_parents_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_site_last_week_stats_parameters() {
        return (
            array()
        );
    }

    public static function get_course_daily_stats_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_last_user_grades_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'limit' => new external_value(PARAM_INT, 'max items to return'),
            )
        );
    }

    public static function system_check_parameters() {
        return (
            array()
        );
    }

    public static function add_parent_role_parameters() {
        return (
            array(
                'child' => new external_value(PARAM_TEXT, 'child username'),
                'parent' => new external_value(PARAM_TEXT, ' parent username'),
            )
        );
    }

    public static function get_paypal_config_parameters() {
        return (
            array()
        );
    }

    public static function update_session_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_cat_name_parameters() {
        return (
            array(
                'cat_id' => new external_value(PARAM_INT, 'category id'),
            )
        );
    }

    public static function courses_abc_parameters() {
        return (
            array(
                'start_chars' => new external_value(PARAM_TEXT, 'Start chars'),
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function teachers_abc_parameters() {
        return (
            array(
                'start_chars' => new external_value(PARAM_TEXT, 'Start chars'),
            )
        );
    }

    public static function teacher_courses_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'Teacher username'),
            )
        );
    }

    public static function user_custom_fields_parameters() {
        return (
            array()
        );
    }

    public static function course_enrol_methods_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function quiz_get_question_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'question id'),
            )
        );
    }

    public static function quiz_get_random_question_parameters() {
        return (
            array(
                'cat_id' => new external_value(PARAM_INT, 'category id'),
                'used_ids' => new external_value(PARAM_TEXT, 'used question ids'),
            )
        );
    }

    public static function quiz_get_correct_answer_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'question id'),
            )
        );
    }

    public static function quiz_get_question_categories_parameters() {
        return (
            array()
        );
    }

    public static function quiz_get_answers_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'question id'),
            )
        );
    }

    public static function get_course_students_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
                'active' => new external_value(PARAM_INT, 'active'),
            )
        );
    }

    public static function my_teachers_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'Username'),
            )
        );
    }

    public static function my_classmates_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function multiple_suspend_enrolment_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'courses' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'course id'),
                        )
                    )
                ),
                'enrol' => new external_value(PARAM_TEXT, 'enrol plugin', VALUE_DEFAULT, 'manual'),
            )
        );
    }

    public static function suspend_enrolment_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_course_resources_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
                 'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_course_mods_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_course_completion_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_course_quizes_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function my_certificates_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'type' => new external_value(PARAM_TEXT, 'type'),
            )
        );
    }

    public static function get_page_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'page id'),
            )
        );
    }

    public static function get_label_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'label id'),
            )
        );
    }

    public static function get_news_item_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'label id'),
            )
        );
    }

    public static function get_my_news_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_my_events_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_my_grades_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_course_grades_by_category_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_my_grades_by_category_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_cohorts_parameters() {
        return (
            array()
        );
    }

    public static function add_cohort_member_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'cohort_id' => new external_value(PARAM_INT, 'cohort id'),
            )
        );
    }

    public static function get_rubrics_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'id'),
            )
        );
    }

    public static function get_grade_user_report_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_my_grade_user_report_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function teacher_get_course_grades_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
                'search' => new external_value(PARAM_TEXT, 'search text'),
            )
        );
    }

    public static function get_group_members_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'group id'),
                'search' => new external_value(PARAM_TEXT, 'search'),
            )
        );
    }

    public static function get_course_groups_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function teacher_get_group_grades_parameters() {
        return (
            array(
                'course_id' => new external_value(PARAM_INT, 'course id'),
                'group_id' => new external_value(PARAM_INT, 'group id'),
                'search' => new external_value(PARAM_TEXT, 'search'),
            )
        );
    }

    public static function create_course_parameters() {
        return (
            array(
                'course' => new external_single_structure(
                    array(
                        'fullname' => new external_value(PARAM_TEXT, 'fullname'),
                        'shortname' => new external_value(PARAM_TEXT, 'shortname'),
                        'summary' => new external_value(PARAM_RAW, 'summary'),
                        'course_lang' => new external_value(PARAM_TEXT, 'lang'),
                        'startdate' => new external_value(PARAM_INT, 'startdate'),
                        'idnumber' => new external_value(PARAM_TEXT, 'idnumber'),
                        'category' => new external_value(PARAM_INT, 'cat id'),
                    )
                )
            )
        );
    }

    public static function update_course_parameters() {
        return (
            array(
                'course' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'id'),
                        'fullname' => new external_value(PARAM_TEXT, 'fullname'),
                        'shortname' => new external_value(PARAM_TEXT, 'shortname'),
                        'summary' => new external_value(PARAM_RAW, 'summary'),
                        'course_lang' => new external_value(PARAM_TEXT, 'lang'),
                        'startdate' => new external_value(PARAM_INT, 'startdate'),
                        'idnumber' => new external_value(PARAM_TEXT, 'idnumber'),
                        'category' => new external_value(PARAM_INT, 'cat id'),
                    )
                )
            )
        );
    }

    public static function add_user_role_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'id' => new external_value(PARAM_INT, 'course id'),
                'roleid' => new external_value(PARAM_INT, 'role id'),
            )
        );
    }

    public static function get_course_parents_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_all_parents_parameters() {
        return (
            array()
        );
    }

    public static function remove_cohort_member_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'cohort_id' => new external_value(PARAM_INT, 'cohort id'),
            )
        );
    }

    public static function multiple_add_cohort_member_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'cohorts' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'cohort id'),
                        )
                    )
                ),
            )
        );
    }

    public static function multiple_remove_cohort_member_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                 'cohorts' => new external_multiple_structure(
                       new external_single_structure(
                          array(
                             'id' => new external_value(PARAM_INT, 'cohort id'),
                          )
                       )
                    ),
            )
        );
    }

    public static function get_courses_and_groups_parameters() {
        return (
            array()
        );
    }

    public static function multiple_enrol_to_course_and_group_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                 'courses' => new external_multiple_structure(
                       new external_single_structure(
                          array(
                             'id' => new external_value(PARAM_INT, 'course id'),
                             'group_id' => new external_value(PARAM_INT, 'group id'),
                          )
                       )
                    ),
                'roleid' => new external_value(PARAM_INT, 'role id'),
            )
        );
    }

    public static function multiple_remove_from_group_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                 'courses' => new external_multiple_structure(
                       new external_single_structure(
                          array(
                             'id' => new external_value(PARAM_INT, 'course id'),
                             'group_id' => new external_value(PARAM_INT, 'group id'),
                          )
                       )
                    ),
            )
        );
    }

    public static function my_all_courses_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'Username'),
            )
        );
    }

    public static function multiple_unenrol_user_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'course id'),
                        )
                    )
                ),
            )
        );
    }

    public static function unenrol_user_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_children_grades_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_children_grade_user_report_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function enrol_user_with_start_date_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'id' => new external_value(PARAM_INT, 'course id'),
                'roleid' => new external_value(PARAM_INT, 'role id'),
                'start_date' => new external_value(PARAM_INT, 'start_date'),
            )
        );
    }

    public static function remove_user_role_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'id' => new external_value(PARAM_INT, 'course id'),
                'roleid' => new external_value(PARAM_INT, 'role id'),
            )
        );
    }

    public static function get_themes_parameters() {
        return (
            array()
        );
    }

    public static function list_courses_scorm_parameters() {
        return (
            array(
                'enrollable_only' => new external_value(PARAM_INT, 'Return only enrollable courses'),
                'sortby' => new external_value(PARAM_TEXT, 'Order field'),
                'guest' => new external_value(PARAM_INT, 'Return only courses for guests'),
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function create_moodle_only_user_parameters() {
        return (
            array(
                'user_data' => new external_single_structure(
                    array(
                        'username' => new external_value(PARAM_TEXT, 'username'),
                        'firstname' => new external_value(PARAM_TEXT, 'fistname'),
                        'lastname' => new external_value(PARAM_TEXT, 'lastname'),
                        'email' => new external_value(PARAM_RAW, 'email'),
                        'password' => new external_value(PARAM_TEXT, 'password'),
                        'city' => new external_value(PARAM_TEXT, 'city'),
                        'country' => new external_value(PARAM_TEXT, 'country'),
                    )
                )
            )
        );
    }

    public static function enrol_user_with_start_and_end_date_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'id' => new external_value(PARAM_INT, 'course id'),
                'roleid' => new external_value(PARAM_INT, 'role id'),
                'start_date' => new external_value(PARAM_INT, 'start_date'),
                'end_date' => new external_value(PARAM_INT, 'end_date'),
            )
        );
    }

    public static function update_course_enrolments_dates_parameters() {
        return (
            array(
                'course_id' => new external_value(PARAM_INT, 'course id'),
                'start_date' => new external_value(PARAM_INT, 'start_date'),
                'end_date' => new external_value(PARAM_INT, 'end_date'),
            )
        );
    }

    public static function get_system_roles_parameters() {
        return (
            array()
        );
    }

    public static function add_system_role_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'roleid' => new external_value(PARAM_INT, 'role id'),
            )
        );
    }

    public static function my_badges_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'Username'),
                'max' => new external_value(PARAM_INT, 'max to return'),
            )
        );
    }

    public static function get_course_grades_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
                'search' => new external_value(PARAM_TEXT, 'search text'),
            )
        );
    }

    public static function get_course_grades_items_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_course_questionnaire_results_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_all_courses_parameters() {
        return (
            array(
                'sortby' => new external_value(PARAM_TEXT, 'Order field'),
            )
        );
    }

    public static function get_events_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'Username'),
                'start_date' => new external_value(PARAM_INT, 'time start'),
                'end_date' => new external_value(PARAM_INT, 'time end'),
                'type' => new external_value(PARAM_TEXT, 'event type'),
                'course_id' => new external_value(PARAM_INT, 'course_id'),
            )
        );
    }

    public static function get_event_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'id'),
            )
        );
    }

    public static function get_certificates_credits_parameters() {
        return (
            array(
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'course id'),
                        )
                    )
                )
            )
        );
    }

    public static function set_section_visible_parameters() {
        return (
            array(
                'course_id' => new external_value(PARAM_INT, 'course_id'),
                'section' => new external_value(PARAM_INT, 'section'),
                'active' => new external_value(PARAM_INT, 'active'),
            )
        );
    }

    public static function create_events_parameters() {
        return (
            array(
                'events' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_TEXT, 'name'),
                            'description' => new external_value(PARAM_TEXT, 'description'),
                            'courseid' => new external_value(PARAM_INT, 'course id'),
                            'timestart' => new external_value(PARAM_INT, 'time start'),
                            'timeend' => new external_value(PARAM_INT, 'time end'),
                            'eventtype' => new external_value(PARAM_TEXT, 'event type'),
                            'username' => new external_value(PARAM_TEXT, 'username'),
                        )
                    )
                )
            )
        );
    }

    public static function get_courses_not_editing_teachers_parameters() {
        return (
            array(
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'course id'),
                        )
                    )
                )
            )
        );
    }

    public static function get_course_progress_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'id'),
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function my_courses_progress_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function set_course_visible_parameters() {
        return (
            array(
                'course_id' => new external_value(PARAM_INT, 'course_id'),
                'active' => new external_value(PARAM_INT, 'active'),
            )
        );
    }

    public static function my_completed_courses_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function users_completed_courses_parameters() {
        return (
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'username' => new external_value(PARAM_TEXT, 'username'),
                        )
                    )
                )
            )
        );
    }

    public static function remove_parent_role_parameters() {
        return (
            array(
                'child' => new external_value(PARAM_TEXT, 'child username'),
                'parent' => new external_value(PARAM_TEXT, ' parent username'),
            )
        );
    }

    public static function get_completed_course_users_parameters() {
        return (
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function change_username_parameters() {
        return (
            array(
                'old_username' => new external_value(PARAM_TEXT, 'old username'),
                'new_username' => new external_value(PARAM_TEXT, 'new username')
            )
        );
    }

    public static function my_courses_and_groups_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'Username')
            )
        );
    }

    public static function create_groups_parameters() {
        return (
            array(
                'groups' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'id of course'),
                            'name' => new external_value(PARAM_TEXT, 'multilang compatible name, course unique'),
                            'description' => new external_value(PARAM_RAW, 'group description text'),
                            'descriptionformat' => new external_format_value('description', VALUE_DEFAULT),
                            'enrolmentkey' => new external_value(PARAM_RAW, 'group enrol secret phrase', VALUE_OPTIONAL),
                            'idnumber' => new external_value(PARAM_RAW, 'id number', VALUE_OPTIONAL)
                        )
                    ), 'List of group object. A group has a courseid, a name, a description and an enrolment key.'
                )
            )
        );
    }

    public static function get_category_categories_and_courses_parameters() {
        return (
            array(
                'category' => new external_value(PARAM_INT, 'category id'),
            )
        );
    }

    public static function my_enrolments_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'Username')
            )
        );
    }

    public static function my_courses_completion_progress_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
            )
        );
    }

    public static function get_mentees_certificates_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'type' => new external_value(PARAM_TEXT, 'type')
            )
        );
    }

    public static function get_moodle_version_parameters() {
        return (
            array()
            );
    }

    public static function enable_user_parameters() {
        return (
            array(
                'username' => new external_value(PARAM_TEXT, 'username'),
                'suspended' => new external_value(PARAM_INT, 'suspended')
            )
        );
    }

    public static function get_course_users_parameters() {
        return (
            array(
                'course_id' => new external_value(PARAM_INT, 'course_id')
            )
        );
    }
}
