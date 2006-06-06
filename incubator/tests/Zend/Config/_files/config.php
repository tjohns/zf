<?php
$config['all'] = array(
    'hostname' => 'all',
    'name' => 'thisname',
    'db' => array(
        'host' => '127.0.0.1',
        'user' => 'username',
        'pass' => 'password',
        'name' => 'live',
    ),
    'one' => array(
        'two' => array(
            'three' => 'multi'
        )
    )
);

$config['staging'] = $config['all'];
$config['staging']['hostname'] = 'staging';
$config['staging']['db']['name'] = 'dbstaging';
$config['staging']['debug'] = false;

$config['other_staging'] = $config['staging'];
$config['other_staging']['only_in'] = 'otherStaging';
$config['other_staging']['db']['pass'] = 'anotherpwd';

// invalid section
$config['extendserror'] = 123;
