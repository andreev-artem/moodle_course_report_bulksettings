<?php

function check_action_capabilities($action, $context = NULL, $require = false) {
    global $CFG;
    $requirecapability = NULL;
    if (file_exists($CFG->dirroot.'/course/report/bulksettings/actions/'.$action.'/settings.php')) {
        include($CFG->dirroot.'/course/report/bulksettings/actions/'.$action.'/settings.php');
    }

    if (is_null($requirecapability)) {
        if ($require) {
            print_error('action_nocaps');
        }
        return false;
    } else if (is_string($requirecapability)) {
        $caps = array( $requirecapability );
    } else if (is_array($requirecapability)) {
        $caps = $requirecapability;
    } else {
        if ($require) {
            print_error('action_nocaps');
        }
        return false;
    }
    
    if ($context == NULL) {
        $context = get_context_instance(CONTEXT_SYSTEM);
    }

    foreach ($caps as $cap) {
        if ($require) {
            require_capability($cap, $context);
        } else {
            if (!has_capability($cap, $context)) {
                return false;
            }
        }
    }
    
    return true;
}

?>
