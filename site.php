<?php

use \Hcode\Page;
use \Hcode\Model\Product;

$app->get('/', function() {

	$products = Product::listAll();
	
	$page = new Page();

	$page->setTpl("index", [
		'products' => Product::checkList($products)
	]);

});//Quando chegar nesta linha, será chamado o desctruct do Page (que incluirá o Destruct)


?>