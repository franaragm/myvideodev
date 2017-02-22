<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BackendBundle\Entity\User;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserController extends Controller
{
    public function newUserAction(Request $request)
    {
        $helpers = $this->get("app.apirest.helpers");

        $json = $request->get("json", null);
        $params = json_decode($json);

        if ($json != null) {

            $createdAt = new \DateTime("now");
            $active = 1;
            $imageProfile = null;
            $imageBanner = null;
            $role = "ROLE_USER";
            $userIdentifier = $helpers->uid();
            $email = (isset($params->email)) ? $params->email : null;
            $password = (isset($params->password)) ? $params->password : null;
            $name = (isset($params->name) && ctype_alpha($params->name)) ? $params->name : null;
            $surname = (isset($params->surname) && ctype_alpha($params->surname)) ? $params->surname : null;
            $description = (isset($params->description)) ? $params->description : null;
            $nick = (isset($params->nick)) ? $params->nick : null;

            //validar email
            $validator = $this->container->get('validator');
            $constraints = array(
                new Email(['message'=>'This is not the correct email format']),
                new NotBlank(['message' => 'Email field can not be blank'])
            );
            $errors_email = $validator->validate(
                $email,
                $constraints
            );

            if ($email != null && count($errors_email) == 0 &&
                $password != null && $name != null && $surname != null && $nick != null) {

                $user = new User();
                $user->setCreatedAt($createdAt);
                $user->setImageProfile($imageProfile);
                $user->setImageBanner($imageBanner);
                $user->setActive($active);
                $user->setUserIdentifier($userIdentifier);
                $user->setEmail($email);
                $user->setPassword($password);
                $user->setName($name);
                $user->setSurname($surname);
                $user->setDescription($description);
                $user->setNick($nick);
                $user->setRole($role);

                $em = $this->getDoctrine()->getManager();
                $user_repo = $em->getRepository("BackendBundle:User");
                $isset_user_email = $user_repo->findBy(array(
                   "email" => $email
                ));
                $isset_user_nick = $user_repo->findBy(array(
                    "nick" => $nick
                ));

                switch (true) {
                    case (count($isset_user_email) == 0 && count($isset_user_nick) == 0):
                        $em->persist($user);
                        $em->flush();

                        $data = array(
                            "status" => "success",
                            "msg" => "New user created!!"
                        );
                        break;
                    case (count($isset_user_email) != 0):
                        $data = array(
                            "status" => "error",
                            "code" => 400,
                            "msg" => "User not created, email duplicated!!"
                        );
                        break;
                    case (count($isset_user_nick) != 0):
                        $data = array(
                            "status" => "error",
                            "code" => 400,
                            "msg" => "User not created, nick duplicated!!"
                        );
                        break;
                }

            } elseif (count($errors_email) != 0) {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "msg" => $errors_email[0]->getMessage()
                );
            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "msg" => "Campos obligatorios sin rellenar"
                );
            }

        } else {
            $data = array(
                "status" => "error",
                "code" => 400,
                "msg" => "User not created"
            );
        }

        return $helpers->getjson($data);
    }

}
