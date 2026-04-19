<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Auth routes
$routes->get('login',                          'AuthController::loginForm');
$routes->post('login',                         'AuthController::loginProcess');
$routes->post('logout',                        'AuthController::logout');
$routes->get('logout',                         'AuthController::logoutGet');
$routes->get('forgot-password',                'AuthController::forgotPasswordForm');
$routes->post('forgot-password',               'AuthController::forgotPasswordProcess');
$routes->get('reset-password/(:segment)',      'AuthController::resetPasswordForm/$1');
$routes->post('reset-password/(:segment)',     'AuthController::resetPasswordProcess/$1');
