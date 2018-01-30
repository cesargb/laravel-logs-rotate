<?php

return [
    'log_max_files' => config('app.log_max_files'),

    'log_compress_files' => true,

    'logs_rotate_schedule' => '0 0 * * *',
];
