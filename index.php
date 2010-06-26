<?php  // $Id: index.php,v 1.0 2009/02/05 argentum@cdp.tsure.ru Exp $

require_once('../../../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/course/report/bulksettings/lib.php');

$id    = required_param('id', PARAM_INT);                 // course id.
$apply = optional_param('apply', false, PARAM_BOOL);
$action = optional_param('action', NULL, PARAM_RAW); 

if (!$course = get_record('course', 'id', $id)) {
    print_error('invalidcourse');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $id);
require_capability('coursereport/bulksettings:view', $context);

// check if an action should be performed and do so using plugin list
if ($apply) {
    if (!$action) {
        redirect('index.php?id='. $id);
    }
    
    $SESSION->bulksettingscms = array();
    foreach ($_POST as $k => $v) {
        if (preg_match('#^activity_(\d+)$#',$k,$m)) {
            $SESSION->bulksettingscms[] = $m[1];
        }
    }

    if (empty($SESSION->bulksettingscms)) {
        redirect('index.php?id='. $id);
    }
    
    redirect($CFG->wwwroot.'/course/report/bulksettings/actions/'.$action.'/index.php?id='.$id);
}

$strbulksettings = get_string('title', 'report_bulksettings');
$strreports    = get_string('reports');

$langdir = $CFG->dirroot.'/course/report/bulksettings/lang/';
$pluginname = 'report_bulksettings';

$navlinks = array();
$navlinks[] = array('name' => $strreports, 'link' => "../../report.php?id=$course->id", 'type' => 'misc');
$navlinks[] = array('name' => $strbulksettings, 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);
print_header("$course->shortname: $strbulksettings", $course->fullname, $navigation);

?>

<script type="text/javascript">
//<![CDATA[
function toggle_section(section, checked) {
    var checkboxes = getElementsByClassName(document, 'input', 'section' + section);
    for (i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = checked;
    }
}
//]]>
</script>

<?php

echo '<form action="index.php?id=' . $id . '" method="POST" >';

echo '<br />';
echo '<a href="javascript: checkall();">'.get_string('selectall').'</a> / '.
     '<a href="javascript: checknone();">'.get_string('deselectall').'</a> ';
echo '<br /><br />';

$modinfo =& get_fast_modinfo($course);

foreach ($modinfo->sections as $sectionnum => $section) {
    echo '<div class="bssection">';
    echo '<a href="javascript: toggle_section('.$sectionnum.', 1);">'.get_string('selectall').'</a> / '.
         '<a href="javascript: toggle_section('.$sectionnum.', 0);">'.get_string('deselectall').'</a> ';
    echo '<br />';
    foreach ($section as $cmnum) {
        $cm = $modinfo->cms[$cmnum];
        echo '<input type="checkbox" class="section'.$sectionnum.'" name="activity_'.$cm->id.'" />';
        if (!$cm->visible) {
            echo '<span class="bshidden">';
        }
        if ($cm->modname == 'label') {
            $name = $cm->extra;
        } else {
            $name = $cm->name;
        }
        echo format_string($name, true, $id);
        if (!$cm->visible) {
            echo "</span>";
        }
        echo "<br />\n";
    }
    echo '</div>';    
}

echo '<div align="center">';
echo '<select name="action" size=1>';

echo "<option value=0>". get_string('choose').'... </option>';
$plugins = get_list_of_plugins('course/report/bulksettings/actions/', 'CVS');
foreach ($plugins as $dir) {
    if (check_action_capabilities($dir, $context)) {
        $action = get_string('pluginname', 'bulksettings_'.$dir, NULL, $CFG->dirroot.'/course/report/bulksettings/actions/'.$dir.'/lang/');
        echo "<option value=$dir>". s($action). '</option>';
    }
}

echo '</select>';
echo '<br /><br /><input type=submit name="apply" value="' . get_string('go') . '">';
echo '</div>';

print_footer();
?>
