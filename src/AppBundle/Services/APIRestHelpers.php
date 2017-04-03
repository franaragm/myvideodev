<?php

namespace AppBundle\Services;

use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;


class APIRestHelpers
{
    public $jwt_auth;

    public function __construct($jwt_auth)
    {
        $this->jwt_auth = $jwt_auth;
    }

    /**
     * Comprueba Token cifrado y devuelve bool o token decodificado
     *
     * @param string $hash token cifrado
     * @param bool $getIdentity
     * @return bool|object
     */
    public function authCheck($hash, $getIdentity = false)
    {
        $jwt_auth = $this->jwt_auth;

        if ($hash != null) {
            if ($getIdentity == false) {
                $check_token = $jwt_auth->checkToken($hash);
            } else {
                $check_token = $jwt_auth->checkToken($hash, true);
            }
        }

        return $check_token;
    }

    /**
     * Retorna array en formato JSON
     *
     * @param array $data el array puede contener objetos
     * @return Response
     */
    public function getjson($data) {
        $normalizers = array(new GetSetMethodNormalizer());
        $encoders = array("json" => new JsonEncoder());

        $serializer = new Serializer($normalizers, $encoders);
        $json = $serializer->serialize($data, 'json');

        $response = new Response();
        $response->setContent($json);
        $response->headers->set("Content-Type", "application/json");

        return $response;
    }

    /**
     * Genera identificador único para el usuario
     *
     * @return bool|string
     */
    public function uid() {
        mt_srand((double)microtime()*1000000);
        $token = mt_rand(1, mt_getrandmax());

        $uid = uniqid(md5($token), true);
        if($uid != false && $uid != '' && $uid != NULL) {
            $out = sha1($uid);
            return $out;
        } else {
            return false;
        }
    }

    /**
     * Genera identificador único para el video
     *
     * @return bool|string
     */
    public function vid() {
        mt_srand((double)microtime()*1000000);
        $token = mt_rand(1, mt_getrandmax());
        $letter = chr(65 + rand(0, 25));

        $vid = uniqid(md5($token), true);
        if($vid != false && $vid != '' && $vid != NULL) {
            $vid .= $letter;
            $out = sha1($vid);
            return $out;
        } else {
            return false;
        }
    }
}