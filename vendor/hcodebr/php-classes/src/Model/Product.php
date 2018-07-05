<?php

namespace Hcode\Model;

use \Hcode\Model;
use \Hcode\Mailer;
use \Hcode\DB\Sql;

class Product extends Model {

    public static function listAll(){
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");
    }

    public function save(){
        $sql = new Sql();

        $results = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(
            ":idproduct" => $this->getidproduct(),
            ":desproduct" => $this->getdesproduct(),
            ":vlprice" => $this->getvlprice(),
            ":vlwidth" => $this->getvlwidth(),
            ":vlheight" => $this->getvlheight(),
            ":vllength" => $this->getvllength(),
            ":vlweight" => $this->getvlweight(),
            ":desurl" => $this->getdesurl()
        ));

        $this->setData($results[0]);
    }

    public function get($idproduct){
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", array(
            ":idproduct" => $idproduct
        ));

        $this->setData($results[0]);
    }

    public function delete(){
        $sql = new Sql();

        $sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", array(
            ":idproduct" => $this->getidproduct()
        ));
    }

    public function checkPhoto(){
        if(file_exists($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
            "res" . DIRECTORY_SEPARATOR . 
            "site" . DIRECTORY_SEPARATOR . 
            "img" . DIRECTORY_SEPARATOR . 
            "products" . DIRECTORY_SEPARATOR . 
            $this->getidproduct() . ".jpg"
        )){
            //URL, portanto não usa directory_separator
            $url =  "/res/site/img/products/" . $this->getidproduct() . ".jpg";
        } else {
            $url =  "/res/site/img/product.jpg";
        }

        return $this->setdesphoto($url);
    }

    public function getValues(){

        $this->checkPhoto();

        $values = parent::getValues();

        return $values;
    }

    public function setPhoto($file){

        //Pega o nome do arquivo onde tem ponto, e separa, fazendo um array nele
        $extension = explode('.', $file['name']);

        //Pega a última posição desse array
        $extension = end($extension);

        switch($extension){
            case "jpg":
            case "jpeg":
            //tmp_name = Nome temporário do arq q está no servidor
            //Aí a imagem já fica na variável $image!
            $image = imagecreatefromjpeg($file['tmp_name']);
            break;
        case "gif":
            $image = imagecreatefromgif($file['tmp_name']);
            break;
        case "png":
            $image = imagecreatefrompng($file['tmp_name']);
            break;
        }

        $dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
        "res" . DIRECTORY_SEPARATOR . 
        "site" . DIRECTORY_SEPARATOR . 
        "img" . DIRECTORY_SEPARATOR . 
        "products" . DIRECTORY_SEPARATOR . 
        $this->getidproduct() . ".jpg";

        imagejpeg($image, $dist);

        imagedestroy($image);

        $this->checkPhoto();

    }

}

?>