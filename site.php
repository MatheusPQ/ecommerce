<?php


use \Hcode\Page;

$app->get('/', function() {
	
	$page = new Page();

	$page->setTpl("index");

});//Quando chegar nesta linha, será chamado o desctruct do Page (que incluirá o Destruct)


?>