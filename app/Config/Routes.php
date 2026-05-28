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

// Dashboard route - protected by auth
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);

// Vendor routes - protected by auth and role
$routes->get('vendors',                    'VendorController::index', ['filter' => 'auth:role:admin,manager']);
$routes->get('vendors/create',             'VendorController::create', ['filter' => 'auth:role:admin,manager']);
$routes->post('vendors/create',            'VendorController::store', ['filter' => 'auth:role:admin,manager']);
$routes->get('vendors/edit/(:num)',        'VendorController::edit/$1', ['filter' => 'auth:role:admin,manager']);
$routes->post('vendors/edit/(:num)',       'VendorController::update/$1', ['filter' => 'auth:role:admin,manager']);
$routes->post('vendors/delete/(:num)',     'VendorController::delete/$1', ['filter' => 'auth:role:admin']);

// Stall routes - protected by auth and role
$routes->get('stalls',                     'StallController::index', ['filter' => 'auth:role:admin,manager']);
$routes->get('stalls/create',              'StallController::create', ['filter' => 'auth:role:admin,manager']);
$routes->post('stalls/create',             'StallController::store', ['filter' => 'auth:role:admin,manager']);
$routes->get('stalls/edit/(:num)',         'StallController::edit/$1', ['filter' => 'auth:role:admin,manager']);
$routes->post('stalls/edit/(:num)',        'StallController::update/$1', ['filter' => 'auth:role:admin,manager']);
$routes->post('stalls/delete/(:num)',      'StallController::delete/$1', ['filter' => 'auth:role:admin']);

// Record routes - protected by auth and role
$routes->get('records',                    'RecordController::index', ['filter' => 'auth:role:admin,manager,cashier']);
$routes->get('records/create',             'RecordController::create', ['filter' => 'auth:role:admin,manager,cashier']);
$routes->post('records/create',            'RecordController::store', ['filter' => 'auth:role:admin,manager,cashier']);
$routes->get('records/edit/(:num)',        'RecordController::edit/$1', ['filter' => 'auth:role:admin,manager']);
$routes->post('records/edit/(:num)',       'RecordController::update/$1', ['filter' => 'auth:role:admin,manager']);
$routes->post('records/delete/(:num)',     'RecordController::delete/$1', ['filter' => 'auth:role:admin']);

// Profile routes - protected by auth
$routes->get('profile',                    'UserController::profile', ['filter' => 'auth']);
$routes->post('profile',                   'UserController::updateProfile', ['filter' => 'auth']);

// User Management routes - admin only
$routes->get('users',                      'UserController::index', ['filter' => 'auth:role:admin']);
$routes->get('users/create',               'UserController::create', ['filter' => 'auth:role:admin']);
$routes->post('users/create',              'UserController::store', ['filter' => 'auth:role:admin']);
$routes->get('users/edit/(:num)',          'UserController::edit/$1', ['filter' => 'auth:role:admin']);
$routes->post('users/edit/(:num)',         'UserController::update/$1', ['filter' => 'auth:role:admin']);
$routes->post('users/delete/(:num)',       'UserController::delete/$1', ['filter' => 'auth:role:admin']);
