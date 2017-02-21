<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;


class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        $helpers = $this->get("app.apirest.helpers");

        $hash = $request->get("authorization", null);
        $check = $helpers->authCheck($hash, true);

        var_dump($check);
        die();

        //$em = $this->getDoctrine()->getManager();
        //$users = $em->getRepository('BackendBundle:User')->findAll();

        //return $helpers->getjson($users);
    }

    /**
     * Recibe datos para login, valida email y
     * devuelve JSON con respuesta que puede estar codificada
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        $helpers = $this->get("app.apirest.helpers");
        $jwt_auth = $this->get("app.jwt.auth");

        // Recibir JSON por POST
        $json = $request->get("json", null);

        if($json != null){
            $params = json_decode($json);

            $email = (isset($params->email)) ? $params->email : null;
            $password = (isset($params->password)) ? $params->password : null;
            $getHash = (isset($params->getHash)) ? $params->getHash : null;

            //validar email
            $validator = $this->container->get('validator');

            $constraints = array(
                new Email(['message'=>'This is not the correct email format']),
                new NotBlank(['message' => 'This field can not be blank'])
            );

            $errors_email = $validator->validate(
                $email,
                $constraints
            );

            if (count($errors_email) == 0 && $password != null){

                if ($getHash == null) {
                    $signup = $jwt_auth->signup($email, $password);
                } else {
                    $signup = $jwt_auth->signup($email, $password, true);
                }

                // para responder con objetos en formato JSON
                return new JsonResponse($signup);

            } else {
                return $helpers->getjson(array(
                   "status" => "error",
                    "data" => $errors_email[0]->getMessage()
                ));
            }

        } else {
            return $helpers->getjson(array(
                "status" => "error",
                "data" => "send json with post"
            ));
        }
    }

}
