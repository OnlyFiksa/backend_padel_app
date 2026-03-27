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
    $routes->get('venues', 'Venue::index'); 
    $routes->get('venues/(:num)', 'Explore::getVenueDetails/$1');

    // Endpoint Transaksi (Booking)
    $routes->post('bookings', 'Booking::create');                          
    $routes->get('bookings/user/(:num)', 'Booking::getUserBookings/$1');
    $routes->post('bookings/cancel/(:num)', 'Booking::cancelBooking/$1');
    $routes->post('bookings/confirm-payment', 'Booking::confirmPayment');  

    // Endpoint Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // Endpoint Profile
    $routes->post('profile/update', 'Profile::updateProfile');
    $routes->post('password/update', 'Profile::updatePassword');
    $routes->get('profile/stats/(:num)', 'Profile::stats/$1');

    // Endpoint Jadwal & Promo
    $routes->get('schedules/available', 'Schedule::available');
    $routes->post('promos/apply', 'Promo::apply');
});