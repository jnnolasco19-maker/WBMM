<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'AuthController::login');

// Auth
$routes->match(['GET', 'POST'], 'login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

// Dashboard
$routes->get('dashboard', 'DashboardController::index');

// Notifications
$routes->get('notifications', 'NotificationController::index');

// Stalls
$routes->get('stalls', 'StallController::index');
$routes->match(['GET', 'POST'], 'stalls/create', 'StallController::create', ['filter' => 'role:admin']);
$routes->match(['GET', 'POST'], 'stalls/edit/(:num)', 'StallController::edit/$1', ['filter' => 'role:admin']);
$routes->post('stalls/delete/(:num)', 'StallController::delete/$1', ['filter' => 'role:admin']);
$routes->get('stalls/view/(:num)', 'StallController::view/$1');

// Vendors
$routes->get('vendors', 'VendorController::index');
$routes->match(['GET', 'POST'], 'vendors/create', 'VendorController::create', ['filter' => 'role:admin,staff']);
$routes->match(['GET', 'POST'], 'vendors/edit/(:num)', 'VendorController::edit/$1', ['filter' => 'role:admin']);
$routes->post('vendors/delete/(:num)', 'VendorController::delete/$1', ['filter' => 'role:admin']);
$routes->get('vendors/view/(:num)', 'VendorController::view/$1');

// Assignments
$routes->get('assignments', 'AssignmentController::index', ['filter' => 'role:admin,staff']);
$routes->match(['get', 'post'], 'assignments/create', 'AssignmentController::create', ['filter' => 'role:admin,staff']);
$routes->match(['get', 'post'], 'assignments/edit/(:num)', 'AssignmentController::edit/$1', ['filter' => 'role:admin,staff']);
$routes->post('assignments/terminate/(:num)', 'AssignmentController::terminate/$1', ['filter' => 'role:admin']);

// Payments
$routes->get('payments', 'PaymentController::index');
$routes->match(['GET', 'POST'], 'payments/create', 'PaymentController::create');
$routes->get('payments/receipt/(:num)', 'PaymentController::receipt/$1');
$routes->get('payments/receipt/(:num)/pdf', 'PaymentController::receiptPdf/$1');
$routes->get('payments/ajax/vendor/(:num)', 'PaymentController::ajaxVendor/$1');
$routes->get('payments/ajax/compute', 'PaymentController::ajaxCompute');

// Records & Reports
$routes->get('records', 'RecordController::index');
$routes->get('records/summary', 'RecordController::summary', ['filter' => 'role:admin,supervisor']);
$routes->get('records/export', 'RecordController::export');
$routes->get('records/summary/export', 'RecordController::exportSummary', ['filter' => 'role:admin,supervisor']);
$routes->get('records/overdue', 'RecordController::overdue');
$routes->get('records/overdue/export', 'RecordController::exportOverdue');
$routes->get('records/vacant', 'RecordController::vacant');
$routes->get('records/vacant/export', 'RecordController::exportVacant');
$routes->get('records/permits', 'RecordController::permits');
$routes->get('records/permits/export', 'RecordController::exportPermits');

// Collector Remittance
$routes->get('reports/collector', 'ReportController::collector', ['filter' => 'role:admin,supervisor']);
$routes->get('reports/collector/export', 'ReportController::collectorExport', ['filter' => 'role:admin,supervisor']);
$routes->get('reports/collector/(:num)', 'ReportController::collectorDetail/$1', ['filter' => 'role:admin,supervisor']);

// Rates
$routes->get('rates', 'RateController::index', ['filter' => 'role:admin']);
$routes->match(['GET', 'POST'], 'rates/create', 'RateController::create', ['filter' => 'role:admin']);

// Users
$routes->get('users', 'UserController::index', ['filter' => 'role:admin']);
$routes->match(['GET', 'POST'], 'users/create', 'UserController::create', ['filter' => 'role:admin']);
$routes->match(['GET', 'POST'], 'users/edit/(:num)', 'UserController::edit/$1', ['filter' => 'role:admin']);
$routes->post('users/deactivate/(:num)', 'UserController::deactivate/$1', ['filter' => 'role:admin']);
