<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Landing page
$routes->get('/', 'Home::index');

// Authentication
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::doRegister');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::doLogin');
$routes->get('logout', 'Auth::logout');

// Dashboard warga (map)
$routes->get('dashboard', 'Dashboard::index');

// Complaint
$routes->get('complaint/create', 'Complaint::create');
$routes->post('complaint/store', 'Complaint::store');
$routes->get('complaint/check-duplicate', 'Complaint::checkDuplicate');
$routes->get('complaint/(:num)', 'Complaint::detail/$1');
$routes->post('api/complaint/upvote', 'Api::upvote');
$routes->post('api/complaint/nearby', 'Api::nearby');

// Admin
$routes->get('admin', 'Admin::index');
$routes->get('admin/datatable', 'Admin::datatable');
$routes->get('admin/analytics', 'Admin::analytics');
$routes->get('admin/users', 'Admin::users');
$routes->get('admin/categories', 'Admin::categories');

// API - JSON endpoints
$routes->get('api/map-data', 'Api::mapData');
$routes->get('api/heatmap-data', 'Api::heatmapData');
$routes->get('api/chart-data', 'Api::chartData');
$routes->post('api/admin/update-status', 'Api::updateStatus');
$routes->post('api/admin/create-user', 'Api::createUser');
$routes->post('api/admin/update-user', 'Api::updateUser');
$routes->post('api/admin/delete-user', 'Api::deleteUser');
$routes->post('api/admin/create-category', 'Api::createCategory');
$routes->post('api/admin/update-category', 'Api::updateCategory');
$routes->post('api/admin/delete-category', 'Api::deleteCategory');
$routes->get('api/admin/datatable-data', 'Api::datatableData');
$routes->get('api/admin/list-users', 'Api::listUsers');
$routes->get('api/admin/list-categories', 'Api::listCategories');
