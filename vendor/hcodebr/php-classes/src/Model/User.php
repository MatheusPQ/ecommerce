<?php

namespace Hcode\Model;

use \Hcode\Model;
use \Hcode\Mailer;
use \Hcode\DB\Sql;

class User extends Model {

    const SESSION = "User"; //nome da sessão

    //Pelo menos 16 caracteres
    const SECRET = "HcodePhp7_Secret";

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

    public static function listAll(){
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
    }

    public function save(){
        $sql = new Sql();

        $results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":desperson"    =>$this->getdesperson(),
            ":deslogin"     =>$this->getdeslogin(),
            ":despassword"  =>$this->getdespassword(),
            ":desemail"     =>$this->getdesemail(),
            ":nrphone"      =>$this->getnrphone(),
            ":inadmin"      =>$this->getinadmin()
        ));

        $this->setData($results[0]);
    }

    public function get($iduser){
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
            ":iduser"=>$iduser
        ));

        $this->setData($results[0]);
    }

    public function update(){

        $sql = new Sql();

        $results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":iduser"       => $this->getiduser(),
            ":desperson"    => $this->getdesperson(),
            ":deslogin"     => $this->getdeslogin(),
            ":despassword"  => $this->getdespassword(),
            ":desemail"     => $this->getdesemail(),
            ":nrphone"      => $this->getnrphone(),
            ":inadmin"      => $this->getinadmin()
        ));

        $this->setData($results[0]);
    }

    public function delete(){
        $sql = new Sql();

        $sql->query("Call sp_users_delete(:iduser)", array(
            ":iduser" => $this->getiduser()
        ));
    }

    public static function getForgot($email, $inadmin = true){
        $sql = new Sql();

        $results = $sql->select("
            SELECT * 
            FROM tb_persons a 
            INNER JOIN tb_users b USING (idperson) 
            WHERE a.desemail = :email;
        ", array(
            ":email"=>$email
        ));

        if(count($results) === 0){
            throw new \Exception("Não foi possível recuperar a senha.");
        } else {
            $data = $results[0];
            $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
                ":iduser"=>$data["iduser"],
                ":desip"=>$_SERVER["REMOTE_ADDR"]
            ));

            if(count($results2) === 0){
                throw new \Exception("Não foi possível recuperar a senha.");
            } else {
                $dataRecovery = $results2[0];

                // === ENCRIPTAR ===

                //DEPRECIADO, NÃO USAR
                // $code = base64_encode( mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB) );

                $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
                $code = openssl_encrypt($dataRecovery['idrecovery'], 'aes-256-cbc', User::SECRET, 0, $iv);
                $result = base64_encode($iv.$code);
                
                if ($inadmin === true) {
                    $link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$result";
                } else {
                    $link = "http://www.hcodecommerce.com.br/forgot/reset?code=$result";
                }

                // $link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";

                // ========================================================================

                $mailer = new Mailer(
                    $data["desemail"], 
                    $data["desperson"], 
                    "Redefinir senha do Ecommerce", 
                    "forgot", //Nome do template do email.. forgot.html
                    array(
                        "name"=>$data["desperson"],
                        "link"=>$link //name e link são os nomes das variáveis dentro do template do email (forgot.html)
                    )
                );

                $mailer->send();

                return $data;
            }
        }
    }

    public static function validForgotDecrypt($result){

        // $idrecovery = mcrypt_decrypt(
        //     MCRYPT_RIJNDAEL_128, 
        //     User::SECRET, 
        //     base64_decode($code), 
        //     MCRYPT_MODE_ECB
        // );
        
        $result = base64_decode($result);
        $code = mb_substr($result, openssl_cipher_iv_length('aes-256-cbc'), null, '8bit');
        $iv = mb_substr($result, 0, openssl_cipher_iv_length('aes-256-cbc'), '8bit');;
        $idrecovery = openssl_decrypt($code, 'aes-256-cbc', User::SECRET, 0, $iv);

        $sql = new Sql();

        $results = $sql->select("
            SELECT *
            FROM tb_userspasswordsrecoveries a
            INNER JOIN tb_users b USING(iduser)
            INNER JOIN tb_persons c USING(idperson)
            WHERE
                a.idrecovery = :IDRECOVERY
                AND
                a.dtrecovery IS NULL
                AND
                DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
        ", array(
            ":IDRECOVERY" => $idrecovery
        ));

        if(count($results) === 0){
            throw new \Exception("Não foi possível recuperar a senha.");            
        } else {
            return $results[0];
        }
    }

    public static function setForgotUsed($idrecovery){
        $sql = new Sql();

        $sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :IDRECOVERY", array(
            ":IDRECOVERY"=>$idrecovery
        ));
    }

    public function setPassword($password){
        $sql = new Sql();

        $sql->query("UPDATE tb_users SET despassword = :PASSWORD WHERE iduser = :IDUSER", array(
            ":PASSWORD"=>$password,
            ":IDUSER"=>$this->getiduser()
        ));
    }
}

?>