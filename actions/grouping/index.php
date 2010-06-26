<?php

require_once('../../../../../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/course/report/bulksettings/lib.php');

$id    = required_param('id', PARAM_INT);                 // course id.
$apply = optional_param('apply', false, PARAM_BOOL);
$grouponly = optional_param('grouponly', false, PARAM_BOOL);
$grouping = optional_param('grouping', 0, PARAM_INT);

if (!$course = get_record('course', 'id', $id)) {
    print_error('invalidcourse');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $id);
check_action_capabilities('grouping', $context, true);

$langdir = $CFG->dirroot.'/course/report/bulksettings/actions/grouping/lang/';
$pluginname = 'bulksettings_grouping';

$return = $CFG->wwwroot.'/course/report/bulksettings/index.php?id='.$id;

if (empty($CFG->enablegroupings)) {
    notice(get_string('groupingsdisabled', $pluginname, NULL, $langdir), $return);
}

if (empty($SESSION->bulksettingscms)) {
    redirect($return);
}

if ($apply) {
    foreach ($SESSION->bulksettingscms as $cm) {
        set_coursemodule_groupingid($cm, $grouping);
        set_coursemodule_groupmembersonly($cm, $grouponly);
    }
    rebuild_course_cache($id);
    redirect($return, get_string('changessaved'));
}

$strbulksettings = get_string('title', 'report_bulksettings');
$strreports    = get_string('reports');
$strpluginname = get_string('pluginname', $pluginname, NULL, $langdir);

$navlinks = array();
$navlinks[] = array('name' => $strreports, 'link' => "../../report.php?id=$course->id", 'type' => 'misc');
$navlinks[] = array('name' => $strbulksettings, 'link' => "../../index.php?id=$id", 'type' => 'misc');
$navlinks[] = array('name' => $strpluginname, 'link' => NULL, 'type' => 'misc');
$navigation = build_navigation($navlinks);
print_header("$course->shortname: $strbulksettings - $strpluginname", $course->fullname, $navigation);

echo '<form action="index.php?id='. $id. '" method="POST">';
echo '<div align="center">';
echo '<br />';
echo '<input type="checkbox" name="grouponly">'. get_string('groupmembersonly', 'group'). '<br />';
echo get_string('grouping', 'group'). '&nbsp;';
echo '<select name="grouping" size=1>';
$groupings = groups_get_all_groupings($id);
echo "<option value=0>". get_string('none'). '</option>';
foreach ($groupings as $grouping) {
    echo "<option value={$grouping->id}>". s($grouping->name). '</option>';
}
echo '</select><br /><br />';
echo '<input type=submit name="apply" value="'. get_string('go'). '">';
echo '</div>';

print_footer();
?>
