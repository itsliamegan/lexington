<?php declare(strict_types=1);

namespace Lexington;

use Illuminate\Database\Capsule\Manager as DB;
use Lexington\Http\Controller\ErrorsController;
use Vector\Http\Request;

final class App
{
    /** @var DB */
    private static $db;

    public static function configure() : void
    {
        \Vector\App::configure([
            'router' => [
                '\\Lexington\\Http\\Controller',
                __DIR__ . '/routes.php'
            ],

            'views' => [
                __DIR__ . '/View/',
            ],

            'dotenv' => [
                __DIR__ . '/../../'
            ]
        ]);

        $db = new DB;
        $db->addConnection([
            'driver'   => Request::env('DB_DRIVER'),
            'host'     => Request::env('DB_HOST'),
            'database' => Request::env('DB_NAME'),
            'username' => Request::env('DB_USERNAME'),
            'password' => Request::env('DB_PASSWORD')
        ]);
        self::$db = $db;
    }

    public static function boot() : void
    {
        try {
            self::$db->bootEloquent();
            self::$db->setAsGlobal();
            \Vector\App::boot();
        } catch (\Throwable $e) {
            ErrorsController::handle($e);
        }
    }
}
