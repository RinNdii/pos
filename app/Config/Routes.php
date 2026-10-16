<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('auth', 'Auth::index');
$routes->get('items', 'Items::index');
$routes->get('items/create', 'Items::create');
$routes->post('items/store', 'Items::store');
// Modul Autentikasi
$routes->get('auth', 'Auth::index');
$routes->post('auth/process', 'Auth::process');
$routes->get('logout', 'Auth::logout');
$routes->group('receiving', ['filter' => 'role:admin,gudang'], function($routes) {
    $routes->get('/', 'Receiving::index');
    $routes->get('detail/(:num)', 'Receiving::detail/$1');
});
$routes->group('receivings', ['filter' => 'role:admin,gudang'], function($routes) {
    $routes->get('/', 'Receivings::index');
    $routes->get('detail/(:num)', 'Receivings::detail/$1');
    $routes->get('create', 'Receivings::create');
    $routes->post('store', 'Receivings::store');
    $routes->post('delete/(:num)', 'Receivings::delete/$1');
    $routes->get('process/(:num)', 'Receivings::process/$1');
});
$routes->get('warehouse', 'Receivings::index/warehouse', ['filter' => 'role:admin,gudang']);
$routes->get('/', 'Dashboard::index', ['filter' => 'role:admin,kasir,gudang']);
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'role:admin,kasir,gudang']);
$routes->post('items/update/(:num)', 'Items::update/$1');
$routes->get('items/delete/(:num)', 'Items::delete/$1');
$routes->get('auth/logout', 'Auth::logout');

$routes->group('', ['filter' => 'role:admin'], function($routes) {
    // Master Supplier Routes
    $routes->get('suppliers', 'Suppliers::index');
    $routes->post('suppliers/store', 'Suppliers::store');
    $routes->post('suppliers/update/(:num)', 'Suppliers::update/$1');
    $routes->get('suppliers/delete/(:num)', 'Suppliers::delete/$1');
});
$routes->group('', ['filter' => 'role:admin,kasir'], function($routes) {
    // Master Customer Routes
    $routes->get('customers', 'Customers::index');
    $routes->post('customers/store', 'Customers::store');
    $routes->post('customers/update/(:num)', 'Customers::update/$1');
    $routes->get('customers/delete/(:num)', 'Customers::delete/$1');
});
$routes->group('', ['filter' => 'role:admin,gudang'], function($routes) {
    $routes->get('purchases', 'Purchases::index');
    $routes->get('purchases/create', 'Purchases::create');
    $routes->post('purchases/store', 'Purchases::store');
});
$routes->group('', ['filter' => 'role:admin,gudang'], function($routes) {
    $routes->get('receivings', 'Receivings::index');
    $routes->get('receivings/warehouse/detail/(:segment)/(:segment)', 'Receivings::warehouseDetail/$1/$2');
    $routes->post('receivings/warehouse/delete/(:segment)/(:segment)', 'Receivings::deleteWarehouse/$1/$2');
    $routes->post('receivings/warehouse/reduce/(:segment)/(:segment)', 'Receivings::reduceWarehouse/$1/$2');
});
   
   $routes->group('', ['filter' => 'role:gudang'], function($routes) {
    $routes->get('receivings/process/(:num)', 'Receivings::process/$1');
});
$routes->group('', ['filter' => 'role:admin,gudang'], function($routes) {
    // Master Bahan Baku
    $routes->get('raw_materials', 'RawMaterials::index');
    $routes->get('raw_materials/create', 'RawMaterials::create');
    $routes->post('raw_materials/store', 'RawMaterials::store');
    $routes->post('raw_materials/update/(:num)', 'RawMaterials::update/$1');
    $routes->get('raw_materials/delete/(:num)', 'RawMaterials::delete/$1');
});
// 1. Rute Penjualan (Admin dan Kasir)
$routes->group('', ['filter' => 'role:admin,kasir'], function($routes) {
    $routes->get('pos', 'Pos::index');
    $routes->post('pos/checkout', 'Pos::checkout');
});

// 2. Rute Admin & Kasir (Lihat Riwayat)
$routes->group('', ['filter' => 'role:admin,kasir'], function($routes) {
    $routes->get('pos/history', 'Pos::history');
    $routes->get('pos/history/detail/(:num)', 'Pos::detail/$1');
    $routes->post('pos/report/(:num)', 'Pos::saveReport/$1');
});