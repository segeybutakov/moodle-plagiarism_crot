<?php

$handlers = array (

/*
 * Event Handlers
 */
    'assessable_file_uploaded' => array (
        'handlerfile'      => '/plagiarism/crot/lib.php',
        'handlerfunction'  => 'crot_event_file_uploaded',
        'schedule'         => 'cron'
    ),
    'assessable_files_done' => array (
        'handlerfile'      => '/plagiarism/crot/lib.php',
        'handlerfunction'  => 'crot_event_files_done',
        'schedule'         => 'cron'
    ),
    'assessable_content_uploaded' => array (
        'handlerfile'      => '/plagiarism/crot/lib.php',
        'handlerfunction'  => 'crot_event_content_uploaded',
        'schedule'         => 'cron'
    ),
    'mod_created' => array (
        'handlerfile'      => '/plagiarism/crot/lib.php',
        'handlerfunction'  => 'crot_event_mod_created',
        'schedule'         => 'cron'
    ),
    'mod_updated' => array (
        'handlerfile'      => '/plagiarism/crot/lib.php',
        'handlerfunction'  => 'crot_event_mod_updated',
        'schedule'         => 'cron'
    ),
    'mod_deleted' => array (
        'handlerfile'      => '/plagiarism/crot/lib.php',
        'handlerfunction'  => 'crot_event_mod_deleted',
        'schedule'         => 'cron'
    ),

);
