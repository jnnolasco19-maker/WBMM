<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'AuthController::login');

// Auth
$routes->match(['get', 'post'], 'login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

// Dashboard
$routes->get('dashboard', 'DashboardController::index');

// Notifications
$routes->get('notifications', 'NotificationController::index');

// Stalls
$routes->get('stalls', 'StallController::index');
$routes->match(['get', 'post'], 'stalls/create', 'StallController::create');
$routes->match(['get', 'post'], 'stalls/edit/(:num)', 'StallController::edit/$1');
$routes->post('stalls/delete/(:num)', 'StallController::delete/$1');
$routes->get('stalls/view/(:num)', 'StallController::view/$1');

// Vendors
$routes->get('vendors', 'VendorController::index');
$routes->match(['get', 'post'], 'vendors/create', 'VendorController::create');
$routes->match(['get', 'post'], 'vendors/edit/(:num)', 'VendorController::edit/$1');
$routes->post('vendors/delete/(:num)', 'VendorController::delete/$1');
$routes->get('vendors/view/(:num)', 'VendorController::view/$1');

// Assignments
$routes->get('assignments', 'AssignmentController::index');
$routes->match(['get', 'post'], 'assignments/create', 'AssignmentController::create');
$routes->post('assignments/terminate/(:num)', 'AssignmentController::terminate/$1');

// Payments
$routes->get('payments', 'PaymentController::index');
$routes->match(['get', 'post'], 'payments/create', 'PaymentController::create');
$routes->get('payments/receipt/(:num)', 'PaymentController::receipt/$1');
$routes->get('payments/receipt/(:num)/pdf', 'PaymentController::receiptPdf/$1');
$routes->get('payments/ajax/vendor/(:num)', 'PaymentController::ajaxVendor/$1');
$routes->get('payments/ajax/compute', 'PaymentController::ajaxCompute');

// Records & Reports
$routes->get('records', 'RecordController::index');
$routes->get('records/summary', 'RecordController::summary');
$routes->get('records/export', 'RecordController::export');
$routes->get('records/summary/export', 'RecordController::exportSummary');
$routes->get('records/overdue', 'RecordController::overdue');
$routes->get('records/overdue/export', 'RecordController::exportOverdue');
$routes->get('records/vacant', 'RecordController::vacant');
$routes->get('records/vacant/export', 'RecordController::exportVacant');
$routes->get('records/permits', 'RecordController::permits');
$routes->get('records/permits/export', 'RecordController::exportPermits');

// Collector Remittance
$routes->get('reports/collector', 'ReportController::collector');
$routes->get('reports/collector/export', 'ReportController::collectorExport');
$routes->get('reports/collector/(:num)', 'ReportController::collectorDetail/$1');

// Rates
$routes->get('rates', 'RateController::index');
$routes->match(['get', 'post'], 'rates/create', 'RateController::create');

// Users
$routes->get('users', 'UserController::index');
$routes->match(['get', 'post'], 'users/create', 'UserController::create');
$routes->match(['get', 'post'], 'users/edit/(:num)', 'UserController::edit/$1');
$routes->post('users/deactivate/(:num)', 'UserController::deactivate/$1');
