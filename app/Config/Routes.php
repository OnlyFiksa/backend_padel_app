<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    // Endpoint Autentikasi
    $routes->post('register', 'Auth::register');
    $routes->post('login', 'Auth::login');

    // Endpoint Explore (Katalog Padel)
    $routes->get('venues', 'Explore::getVenues');
    $routes->get('venues/(:num)', 'Explore::getVenueDetails/$1');

    // Endpoint Transaksi (Booking)
    $routes->post('bookings/create', 'Booking::create');
    $routes->get('bookings/user/(:num)', 'Booking::getUserBookings/$1');
    $routes->post('bookings/cancel/(:num)', 'Booking::cancelBooking/$1');

    $routes->get('dashboard', 'Dashboard::index');

    // Taruh di bawah rute dashboard kamu
    $routes->post('profile/update', 'Profile::updateProfile');
    $routes->post('password/update', 'Profile::updatePassword');
});
