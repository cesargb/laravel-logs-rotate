<?php

return [
    'logs_rotate_schedule' => '0 0 * * *',

    'log_compress_files' => true,

    'log_max_files' => env('LOG_MAX_FILES', 5),
];
