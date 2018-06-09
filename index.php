<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
$app = new Slim(); //Rotas

$app->config('debug', true);

$app->get('/', function() {
	
	$page = new Page();

	$page->setTpl("index");

});//Quando chegar nesta linha, será chamado o desctruct do Page (que incluirá o Destruct)

$app->run(); //Roda tudo!

 ?>