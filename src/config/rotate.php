<?php

return [
    'log_max_files' => env('LOG_MAX_FILES', 5),

    'log_compress_files' => true,

    'logs_rotate_schedule' => '0 0 * * *',
];
