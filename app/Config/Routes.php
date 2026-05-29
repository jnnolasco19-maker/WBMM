<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::login');

// Auth routes
$routes->get('login',                   'AuthController::login');
$routes->post('login',                  'AuthController::loginProcess');
$routes->get('logout',                  'AuthController::logout');
$routes->post('logout',                 'AuthController::logout');

// Dashboard
$routes->get('dashboard',               'DashboardController::index');
$routes->get('notifications',           'DashboardController::notifications');

// Vendors Module
$routes->get('vendors',                 'VendorController::index');
$routes->get('vendors/create',          'VendorController::create');
$routes->post('vendors/create',         'VendorController::store');
$routes->get('vendors/edit/(:num)',     'VendorController::edit/$1');
$routes->post('vendors/edit/(:num)',    'VendorController::update/$1');
$routes->get('vendors/delete/(:num)',   'VendorController::delete/$1');
$routes->post('vendors/delete/(:num)',  'VendorController::delete/$1');

// Arkalaba Rental Payments Module
$routes->get('payments',                'PaymentController::index');
$routes->get('payments/create',         'PaymentController::create');
$routes->post('payments/create',        'PaymentController::store');
$routes->get('payments/receipt/(:num)', 'PaymentController::receipt/$1');
$routes->get('payments/receipt/(:num)/pdf', 'PaymentController::receiptPdf/$1');

// Records & Reports Module
$routes->get('records',                 'RecordController::index');
$routes->get('records/export',          'RecordController::export');
$routes->get('records/audit-logs',      'RecordController::auditLogs');
$routes->get('records/summary',         'RecordController::summary');
$routes->get('records/summary/export',  'RecordController::exportSummary');

// User Management Module
$routes->get('users',                   'UserController::index');
$routes->get('users/create',            'UserController::create');
$routes->post('users/create',           'UserController::store');
$routes->get('users/edit/(:num)',       'UserController::edit/$1');
$routes->post('users/edit/(:num)',      'UserController::update/$1');