<?php

namespace Hcode\Model;

use \Hcode\Model;
use \Hcode\DB\Sql;

class User extends Model {

    const SESSION = "User"; //nome da sessão

    public static function login($login, $password){
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
            ":LOGIN"=>$login
        ));

        if(count($results) === 0){
            throw new \Exception("Usuário inexistente ou senha inválida.");
        }

        $data = $results[0];

        if(password_verify($password, $data["despassword"]) === true){
            $user = new User();

            $user->setData($data);
            // var_dump($user);

            //Criou a sessão!
            $_SESSION[User::SESSION] = $user->getValues();

            return $user;
            exit;
        } else {
            throw new \Exception("Usuário inexistente ou senha inválida.");
        }
    }

    //Se está ou ñ logado
    public static function verifyLogin($inAdmin = true){
        if( 
            !isset($_SESSION[User::SESSION]) 
            || !$_SESSION[User::SESSION] 
            || !(int)$_SESSION[User::SESSION]["iduser"] > 0  //Se não for maior que zero
            //Pra caso for vazio, quando fzer o cast pra int, se tornará zero
            || (bool)$_SESSION[User::SESSION]["inadmin"] !== $inAdmin
        ){
            header("Location: /admin/login");
            exit;
        }
    }

    public static function logout(){
        $_SESSION[User::SESSION] = NULL;
    }
}

?>