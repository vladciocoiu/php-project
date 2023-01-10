<?php

require_once __DIR__.'/router.php';

// ##################################################
// ##################################################
// ##################################################

// Static GET
// In the URL -> http://localhost
// The output -> Index
get('/project', 'controllers/home_controller.php');

get('/project/register', 'controllers/register_controller.php');
post('/project/register', 'controllers/register_controller.php');

get('/project/login', 'controllers/login_controller.php');
post('/project/login', 'controllers/login_controller.php');

get('/project/logout', 'controllers/logout_controller.php');

get('/project/items', 'controllers/items_controller.php');
post('/project/items', 'controllers/items_controller.php');

get('/project/borrowings', 'controllers/borrowings_controller.php');
post('/project/borrowings', 'controllers/borrowings_controller.php');

get('/project/contact', 'controllers/contact_controller.php');
post('/project/contact', 'controllers/contact_controller.php');

get('/project/stats', 'controllers/stats_controller.php');


