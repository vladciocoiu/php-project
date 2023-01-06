<?php

require_once __DIR__.'/router.php';

// ##################################################
// ##################################################
// ##################################################

// Static GET
// In the URL -> http://localhost
// The output -> Index
get('/project', 'views/index.php');

get('/project/admin', 'views/admin.php');
post('/project/admin', 'views/admin.php');

get('/project/register', 'views/register.php');
post('/project/register', 'views/register.php');

get('/project/login', 'views/login.php');
post('/project/login', 'views/login.php');

get('/project/logout', 'controllers/logout_controller.php');

any('/project/items', 'views/items.php');

get('/project/borrowings', 'views/borrowings.php');
post('/project/borrowings', 'views/borrowings.php');

