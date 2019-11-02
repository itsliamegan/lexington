<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/phpqrcode/qrlib.php';

use Lexington\App;

date_default_timezone_set('UTC');

App::configure();
App::boot();
