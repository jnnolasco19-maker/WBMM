<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::loginForm');

// Auth routes
$routes->get('login',                          'AuthController::loginForm');
$routes->post('login',                         'AuthController::loginProcess');
$routes->post('logout',                        'AuthController::logout');
$routes->get('logout',                         'AuthController::logoutGet');
$routes->get('register',                       'AuthController::registerForm');
$routes->post('register',                      'AuthController::registerProcess');
$routes->get('forgot-password',                'AuthController::forgotPasswordForm');
$routes->post('forgot-password',               'AuthController::forgotPasswordProcess');
$routes->get('reset-password/(:segment)',      'AuthController::resetPasswordForm/$1');
$routes->post('reset-password/(:segment)',     'AuthController::resetPasswordProcess/$1');

// Dashboard route
$routes->get('dashboard', 'DashboardController::index');

// Vendor routes
$routes->get('vendors',                    'VendorController::index');
$routes->get('vendors/create',             'VendorController::create');
$routes->post('vendors/create',            'VendorController::store');
$routes->get('vendors/edit/(:num)',        'VendorController::edit/$1');
$routes->post('vendors/edit/(:num)',       'VendorController::update/$1');
$routes->post('vendors/delete/(:num)',     'VendorController::delete/$1');

// Stall routes
$routes->get('stalls',                     'StallController::index');
$routes->get('stalls/create',              'StallController::create');
$routes->post('stalls/create',             'StallController::store');
$routes->get('stalls/edit/(:num)',         'StallController::edit/$1');
$routes->post('stalls/edit/(:num)',        'StallController::update/$1');
$routes->post('stalls/delete/(:num)',      'StallController::delete/$1');

// Record routes
$routes->get('records',                    'RecordController::index');
$routes->get('records/create',             'RecordController::create');
$routes->post('records/create',            'RecordController::store');
$routes->get('records/edit/(:num)',        'RecordController::edit/$1');
$routes->post('records/edit/(:num)',       'RecordController::update/$1');
$routes->post('records/delete/(:num)',     'RecordController::delete/$1');

// Profile routes
$routes->get('profile',                    'UserController::profile');
$routes->post('profile',                   'UserController::updateProfile');

// User Management routes
$routes->get('users',                      'UserController::index');
$routes->get('users/create',               'UserController::create');
$routes->post('users/create',              'UserController::store');
$routes->get('users/edit/(:num)',          'UserController::edit/$1');
$routes->post('users/edit/(:num)',         'UserController::update/$1');
$routes->post('users/delete/(:num)',       'UserController::delete/$1');
