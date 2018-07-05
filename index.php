<?php 

session_start(); //Inicia a sessão.
require_once("vendor/autoload.php");

use \Slim\Slim;
$app = new Slim(); //Rotas

require_once("site.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-products.php");

$app->config('debug', true);


$app->run(); //Roda tudo!

?>