<?php declare(strict_types=1);

use Lexington\Http\Middleware\VerifyUserIsAuthorized;
use Lexington\Http\Middleware\VerifyUserIsAdmin;
use Vector\Http\Router;

Router::middleware(new VerifyUserIsAuthorized);
Router::middleware(new VerifyUserIsAdmin);

Router::get('/lexington/sign-in', 'AuthController::signIn');
Router::post('/lexington/sign-in', 'AuthController::create');
Router::get('/lexington/sign-out', 'AuthController::signOut');

Router::get('/lexington', 'IndexController::index');

Router::get('/lexington/tickets', 'TicketsController::index');
Router::post('/lexington/tickets/action', 'TicketsController::action');
Router::get('/lexington/tickets/new', 'TicketsController::new');
Router::post('/lexington/tickets/new', 'TicketsController::create');
Router::get('/lexington/tickets/search', 'TicketsController::search');
Router::get('/lexington/tickets/{id}', 'TicketsController::show');
Router::post('/lexington/tickets/{id}', 'TicketsController::update');
Router::post('/lexington/tickets/{id}/next', 'TicketsController::next');
Router::get('/lexington/tickets/{id}/print', 'TicketsController::print');

Router::get('/lexington/devices', 'DevicesController::index');
Router::get('/lexington/devices/new', 'DevicesController::new');
Router::post('/lexington/devices/new', 'DevicesController::create');
Router::get('/lexington/devices/search', 'DevicesController::search');
Router::get('/lexington/devices/search.json', 'DevicesController::searchJson');
Router::get('/lexington/devices/{asset_tag}', 'DevicesController::show');
Router::post('/lexington/devices/{asset_tag}', 'DevicesController::update');

Router::get('/lexington/loaners', 'LoanersController::index');
Router::get('/lexington/loaners/search.json', 'LoanersController::searchJson');

Router::get('/lexington/documentation', 'DocumentationController::index');
Router::get('/lexington/documentation/new', 'DocumentationController::new');
Router::post('/lexington/documentation/new', 'DocumentationController::create');
Router::get('/lexington/documentation/{slug}', 'DocumentationController::show');
Router::get('/lexington/documentation/{slug}/edit', 'DocumentationController::edit');
Router::post('/lexington/documentation/{slug}/edit', 'DocumentationController::update');
Router::post('/lexington/documentation/{slug}/delete', 'DocumentationController::delete');

Router::get('/lexington/feedback', 'FeedbackController::index');
Router::post('/lexington/feedback', 'FeedbackController::create');

Router::get('/lexington/qrcode', 'QRCodeController::index');

Router::get('/lexington/admin', 'AdminController::index');

Router::get('/lexington/admin/statuses', 'StatusesController::index');
Router::get('/lexington/admin/statuses/new', 'StatusesController::new');
Router::post('/lexington/admin/statuses/new', 'StatusesController::create');
Router::get('/lexington/admin/statuses/{code}', 'StatusesController::show');
Router::post('/lexington/admin/statuses/{code}', 'StatusesController::update');

Router::get('/lexington/admin/issues', 'IssuesController::index');
Router::get('/lexington/admin/issues/new', 'IssuesController::new');
Router::post('/lexington/admin/issues/new', 'IssuesController::create');
Router::get('/lexington/admin/issues/{name}', 'IssuesController::show');
Router::post('/lexington/admin/issues/{name}', 'IssuesController::update');

Router::get('/lexington/admin/feedback', 'AdminFeedbackController::index');

Router::get('/lexington/admin/import', 'ImportController::index');
Router::post('/lexington/admin/import/tickets', 'ImportController::importTickets');
Router::post('/lexington/admin/import/lexington-devices', 'ImportController::importDevicesFromLexington');
Router::post('/lexington/admin/import/google-devices', 'ImportController::importDevicesFromGoogle');
