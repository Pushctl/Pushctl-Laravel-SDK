<?php

declare(strict_types=1);

use Illuminate\Support\Env;

return [

    'url' => Env::get('PUSHCTL_URL'),

    'token' => Env::get('PUSHCTL_TOKEN'),

    'timeout' => (int) Env::get('PUSHCTL_TIMEOUT', 10),

];
