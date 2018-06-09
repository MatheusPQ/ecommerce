<?php 

session_start(); //Inicia a sessão.
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
$app = new Slim(); //Rotas

$app->config('debug', true);

$app->get('/', function() {
	
	$page = new Page();

	$page->setTpl("index");

});//Quando chegar nesta linha, será chamado o desctruct do Page (que incluirá o Destruct)

$app->get('/admin', function() {

	User::verifyLogin();
	
	$page = new PageAdmin();

	$page->setTpl("index");

});

$app->get('/admin/login', function() {
	
	$page = new PageAdmin([
		"header" => false,
		"footer" => false
	]);

	$page->setTpl("login");

});

$app->post('/admin/login', function() {

	User::login($_POST["login"], $_POST['password']);

	header("Location: /admin");
	exit;

});

$app->get('/admin/logout', function() {
	
	User::logout();
	header("Location: /admin/login");
	exit;

});
$app->run(); //Roda tudo!

 ?>