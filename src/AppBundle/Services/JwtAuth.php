<?php

namespace AppBundle\Services;

use Doctrine\Common\Proxy\Exception\UnexpectedValueException;
use Firebase\JWT\JWT;

class JwtAuth
{
    public $manager;
    public $key;

    public function __construct($manager)
    {
        $this->manager = $manager;
        $this->key = "clave-secreta";
    }

    /**
     * Autenticación de usuario
     * devuelve los datos de usuario en un objeto o un token cifrado
     * en caso de error retorna array
     *
     * @param string $email
     * @param string $password
     * @param null $getHash
     *
     * @return array
     */
    public function signup($email, $password, $getHash = null)
    {
        $key = $this->key;

        $user = $this->manager->getRepository('BackendBundle:User')->findOneBy(
            array(
                "email" => $email,
                "password" => $password
            )
        );

        $signup = false;
        if(is_object($user)) {
            $signup = true;
        }

        if($signup == true) {
            // atributos que definen el token
            $token = array(
                "sub" => $user->getId(), // Identifica el sujeto del token, por ejemplo un identificador de usuario.
                "email" => $user->getEmail(),
                "name" => $user->getName(),
                "surname" => $user->getSurname(),
                "description" => $user->getDescription(),
                "password" => $user->getPassword(),
                "role" => $user->getRole(),
                "userIdentifier" => $user->getUserIdentifier(),
                "nick" => $user->getNick(),
                "active" => $user->getActive(),
                "imageProfile" => $user->getImageProfile(),
                "imageBanner" => $user->getImageBanner(),
                "iat" => time(), // fecha de creacion
                "exp" => time()+(7*24*60*60) // fecha de expiración a los 7 dias de la creación
            );

            // codificar datos y generar un hash
            $jwt = JWT::encode($token, $key, 'HS256');

            // decodifica datos y retorna objeto
            $decoded = JWT::decode($jwt, $key, array('HS256'));

            if($getHash != null){
                return $jwt;
            } else {
                return $decoded;
            }

        } else {
            $data = array(
                "status" => "error",
                "code" => 400,
                "msg" => "Login failed !!"
            );
            return $data;
        }

    }

    /**
     * Comprueba Token cifrado y devuelve bool o token decodificado
     * segun se le indique con el parametro getIdentity
     *
     * @param string $jwt token cifrado
     * @param bool $getIdentity por defecto false
     * @return bool|object
     */
    public function checkToken($jwt, $getIdentity = false)
    {
        $key = $this->key;
        $auth = false;

        try {
            $decoded = JWT::decode($jwt, $key, array('HS256'));
        } catch (UnexpectedValueException $e) {
            return $auth;
        } catch (\DomainException $e) {
            return $auth;
        }

        //comprueba si token se ha decodificado correctamente
        if (isset($decoded->sub)) {

            $user = $this->manager->getRepository('BackendBundle:User')
                ->findOneBy(array(
                    "id" => $decoded->sub
                ));

            // comprueba si el password del token coincide con el actual de la base de datos
            // evita poder usar tokens de contraseñas antiguas
            if ($user->getPassword() == $decoded->password) {
                $auth = true;
            } else {
                $auth = false;
            }

        } else {
            $auth = false;
        }

        if ($getIdentity == true) {
            return $decoded;
        } else {
            return $auth;
        }

    }

}