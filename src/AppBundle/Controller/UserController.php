<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BackendBundle\Entity\User;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserController extends Controller
{

    /**
     * Crear un nuevo Usuario
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createUserAction(Request $request)
    {
        $helpers = $this->get("app.apirest.helpers");

        $json = $request->get("json", null);

        if ($json != null) {

            $params = json_decode($json);

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

                $pwd = hash('sha256', $password);
                $user->setPassword($pwd);

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
                            "code" => 200,
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

    /**
     * Actualizar datos de perfil de usuario
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateUserAction(Request $request)
    {
        $helpers = $this->get("app.apirest.helpers");

        $hash = $request->get("auth", null);

        if ($hash != null) {
            $authCheck = $helpers->authCheck($hash);
        } else {
            $authCheck = false;
        }

        $json = $request->get("json", null);

        if ($json != null && $authCheck == true) {

            $params = json_decode($json);

            $imageProfile = null;
            $imageBanner = null;
            $password = (isset($params->password)) ? $params->password : null;
            $name = (isset($params->name) && ctype_alpha($params->name)) ? $params->name : null;
            $surname = (isset($params->surname) && ctype_alpha($params->surname)) ? $params->surname : null;
            $description = (isset($params->description)) ? $params->description : null;

            $identity = $helpers->authCheck($hash, true);
            $em = $this->getDoctrine()->getManager();
            $user_repo = $em->getRepository('BackendBundle:User');
            $user = $user_repo->findOneBy(array(
                "id" => $identity->sub
            ));

            $user->setImageProfile($imageProfile);
            $user->setImageBanner($imageBanner);

            if ($password != null) {
                $pwd = hash('sha256', $password);
                $user->setPassword($pwd);
            }

            $user->setName($name);
            $user->setSurname($surname);
            $user->setDescription($description);

            $em->persist($user);
            $em->flush();

            $data = array(
                "status" => "success",
                "code" => 200,
                "msg" => "Data Profile updated!!"
            );

        } else {
            $data = array(
                "status" => "error",
                "code" => 400,
                "msg" => "Auth or json error"
            );
        }

        return $helpers->getjson($data);
    }

    /**
     * Carga de imagenes de perfil de usuario
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadAction(Request $request)
    {
        $helpers = $this->get("app.apirest.helpers");
        $hash = $request->get("auth", null);

        if ($hash != null) {
            $authCheck = $helpers->authCheck($hash);
        } else {
            $authCheck = false;
        }

        $data = array();

        if ($authCheck) {
            $identity = $helpers->authCheck($hash, true);

            $em = $this->getDoctrine()->getManager();
            $user_repo = $em->getRepository('BackendBundle:User');
            $user = $user_repo->findOneBy(array(
                "id" => $identity->sub
            ));

            // upload image files
            $img_profile = $request->files->get("imageProfile");
            $img_banner = $request->files->get("imageBanner");

            $user_media_route = 'uploads/media/'.$user->getUserIdentifier().'_usermedia';

            if (!empty($img_banner) && $img_banner != null) {
                $ext = $img_banner->guessExtension(); // obtencion de extension
                if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png') {
                    $file_name = $user->getUserIdentifier().'_imgbanner_'.time().'.'.$ext;
                    $img_banner->move($user_media_route, $file_name);

                    $user->setImageBanner($file_name);
                    $em->persist($user);
                    $em->flush();

                    $data[] = "Image Banner uploaded";
                } else {
                    $data[] = "Image Banner format not valid";
                }
            }

            if (!empty($img_profile) && $img_profile != null) {
                $ext = $img_profile->guessExtension(); // obtencion de extension
                if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') {
                    $file_name = $user->getUserIdentifier().'_imgprofile_'.time().'.'.$ext;
                    $img_profile->move($user_media_route, $file_name);

                    $user->setImageProfile($file_name);
                    $em->persist($user);
                    $em->flush();

                    $data[] = "Image Profile uploaded";
                } else {
                    $data[] = "Image Profile format not valid";
                }
            }

        } else {
            $data[] = array(
                "status" => "error",
                "code" => 400,
                "msg" => "Auth or json error"
            );
        }

        return $helpers->getjson($data);
    }

}
