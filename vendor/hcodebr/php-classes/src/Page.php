<?php

//Ñ esquecer o namespace!
namespace Hcode;

//Pra quando chamar new tpl, está se referindo ao namespace Rain.
use Rain\Tpl;

class Page {

    private $tpl;
    private $options = [];
    private $defaults = [ //Opções padrão
        "data" => []
    ];

    public function __construct($opts = array()){

        //Mescla dois arrays
        $this->options = array_merge($this->defaults, $opts); //Opts vai sobrescrever o defaults.
        // config tpl
        $config = array(
            //Pasta root!
            "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]."/views/",
            "cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
            "debug"         => false // set to false to improve the speed
        );
    
        Tpl::configure( $config );

        // create the Tpl object
        $this->tpl = new Tpl;


        $this->setData($this->options["data"]);

        $this->tpl->draw("header");

    }

    private function setData($data = array()){
        foreach ($data as $key => $value) {
            $this->tpl->assign($key, $value); //Vai definir as variáveis e seus valores
        }
    }


    //Nome do template, dados,
    public function setTpl($name, $data = array(), $returnHtml = false){

        $this->setData($data);

        return $this->tpl->draw($name, $returnHtml);

    }

    //Último a ser executado
    public function __destruct(){

        $this->tpl->draw("footer");
        
    }

}


?>