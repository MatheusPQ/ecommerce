<?php

namespace Hcode;

class Model {
    private $values = [];

    //name do método chamado, argumentos
    public function __call($name, $args){
        $method = substr($name, 0, 3); //vai ver se é Get ou Set o método
        $fieldName = substr($name, 3, strlen($name)); //de 3 até o final

        // var_dump($method, $fieldName);
        // exit;

        switch($method){
            case "get":
                return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;
                break;

            case "set":
                return $this->values[$fieldName] = $args[0];
                break;
        }
    }

    public function setData($data = array()){

        foreach ($data as $key => $value) {
            //Está sendo criado dinamicamente. A string gerada será executada como um método
            //Deve ser entre chaves
            $this->{"set".$key}($value); 
        }
    }

    public function getValues(){
        return $this->values;
    }
}

?>