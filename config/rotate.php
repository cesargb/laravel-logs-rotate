<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Compression Enable
    |--------------------------------------------------------------------------
    |
    | This option defines if the file rotated must be compressed.
    | If you prefer not compress file, set this value at false.
    */
    'log_compress_files' => true,

    /*
    |--------------------------------------------------------------------------
    | Schedule Rotate
    |--------------------------------------------------------------------------
    |
    | Determine when must be run the cron.
    | You can disable the schedule change the option enable at false.
    | You can change the frequency with option cron.
    |
    */
    'schedule' => [
        'enable' => true,
        'cron' => '0 0 * * *',
    ],

    /*
    |--------------------------------------------------------------------------
    | Max Files Rotated
    |--------------------------------------------------------------------------
    |
    | This value determine the max number of files rotated in the archive folder.
    |
    */
    'log_max_files' => env('LOG_MAX_FILES', 5),

    /*
    |--------------------------------------------------------------------------
    | Other files to rotated
    |--------------------------------------------------------------------------
    |
    | Array the other foreing files
    |
    | Example:
    |   'foreign_files' => [
            storage_path('/logs/worker.log')
    |   ]
    |
    */
    'foreign_files' => [],
];
