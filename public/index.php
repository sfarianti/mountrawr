<?php

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../routes/web.php';

use App\Core\Router;

Router::dispatch();
