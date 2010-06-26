<?php

$coursereport_bulksettings_capabilities = array(

    'coursereport/bulksettings:view' => array(
        'riskbitmask' => RISK_DATALOSS | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),

        'clonepermissionsfrom' => 'moodle/course:update',
    )
);

?>
