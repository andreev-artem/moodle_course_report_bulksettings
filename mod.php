<?php

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    if (has_capability('coursereport/bulksettings:view', $context)) {
        echo '<p>';
        echo "<a href=\"{$CFG->wwwroot}/course/report/bulksettings/index.php?id={$course->id}\">";
        echo get_string('title', 'report_bulksettings')."</a>\n";
        echo '</p>';
    }
?>
