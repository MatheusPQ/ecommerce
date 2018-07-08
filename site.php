<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;

$app->get('/', function() {

	$products = Product::listAll();
	
	$page = new Page();

	$page->setTpl("index", [
		'products' => Product::checkList($products)
	]);

});//Quando chegar nesta linha, será chamado o desctruct do Page (que incluirá o Destruct)

$app->get("/categories/:idcategory", function($idcategory){

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new Page();

	$page->setTpl("category", [
		'category' => $category->getValues(), //ATENÇÃO: usar apóstrofo
		'products' => Product::checkList($category->getProducts())
	]);

});


?>