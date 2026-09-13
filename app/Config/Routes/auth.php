<?php

/*
 * --------------------------------------------------------------------
 * Auth Routes
 * --------------------------------------------------------------------
 */

// Auth routes
$routes->get('/auth/login', 'Auth::login');
$routes->post('/auth/attemptLogin', 'Auth::attemptLogin');
$routes->get('/auth/logout', 'Auth::logout');