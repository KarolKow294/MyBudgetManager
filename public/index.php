<?php

ini_set('session.cookie_lifetime', '864000');

require '../vendor/autoload.php';

error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

session_start();

$router = new Core\Router();

$router->add('api/limit/{id:[\0-9]+}', ['controller' => 'Budget', 'action' => 'limit']);
$router->add('api/total/{id:[\0-9]+}', ['controller' => 'Budget', 'action' => 'total']);

$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('budget', ['controller' => 'Budget', 'action' => 'index']);
$router->add('budget/income/create', ['controller' => 'Budget', 'action' => 'createIncome']);
$router->add('budget/expense/create', ['controller' => 'Budget', 'action' => 'createExpense']);
$router->add('budget/income/edit', ['controller' => 'Budget', 'action' => 'editIncome']);
$router->add('budget/expense/edit', ['controller' => 'Budget', 'action' => 'editExpense']);
$router->add('budget/income/update', ['controller' => 'Budget', 'action' => 'updateIncome']);
$router->add('budget/expense/update', ['controller' => 'Budget', 'action' => 'updateExpense']);
$router->add('budget/income/delete', ['controller' => 'Budget', 'action' => 'deleteIncome']);
$router->add('budget/expense/delete', ['controller' => 'Budget', 'action' => 'deleteExpense']);
$router->add('settings', ['controller' => 'Settings', 'action' => 'index']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('{controller}/{action}');

$router->dispatch($_SERVER['QUERY_STRING']);