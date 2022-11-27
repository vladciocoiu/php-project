<?php

require_once __DIR__.'/router.php';

// ##################################################
// ##################################################
// ##################################################

// Static GET
// In the URL -> http://localhost
// The output -> Index
get('/project', 'views/index.php');
any('/project/register', 'views/register.php');
any('/project/login', 'views/login.php');
get('/project/logout', 'logout.php');
get('/project/search', 'views/search.php');
any('/project/borrowings', 'views/borrowings.php');



// For GET or POST
// The 404.php which is inside the views folder will be called
// The 404.php has access to $_GET and $_POST
any('/404','views/404.php');
