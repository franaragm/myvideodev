<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BackendBundle\Entity\User;
use BackendBundle\Entity\Video;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class VideoController extends Controller
{

    /**
     * Crear nuevo Video
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newVideoAction(Request $request)
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
            $identity = $helpers->authCheck($hash, true);

            $params = json_decode($json);

            $createdAt = new \DateTime('now');
            $updatedAt = new \DateTime('now');
            $videoImage = null;
            $videoSource = null;
            $videoIdentifier = $helpers->vid();
            $status = (isset($params->status)) ? $params->status : null;;
            $title = (isset($params->title)) ? $params->title : null;
            $description = (isset($params->description)) ? $params->description : null;
            $user_id = ($identity->sub != null) ? $identity->sub : null;

            if ($user_id != null && $title != null) {
                $em = $this->getDoctrine()->getManager();
                $user_repo = $em->getRepository('BackendBundle:User');
                $user = $user_repo->findOneBy(array(
                    "id" => $user_id
                ));

                $video = new Video();
                $video->setUser($user);
                $video->setTitle($title);
                $video->setDescription($description);
                $video->setStatus($status);
                $video->setCreatedAt($createdAt);
                $video->setUpdatedAt($updatedAt);
                $video->setVideoIdentifier($videoIdentifier);

                $em->persist($video);
                $em->flush();

                $video = $em->getRepository("BackendBundle:Video")->findOneBy(
                    array(
                        "user" => $user,
                        "title" => $title,
                        "status" => $status,
                        "createdAt" => $createdAt
                    ));

                $data = array(
                    "status" => "success",
                    "code" => 200,
                    "data" => $video
                );

            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "msg" => "Video not created"
                );
            }

        } else {
            $data = array(
                "status" => "error",
                "code" => 400,
                "msg" => "Auth or data error"
            );
        }

        return $helpers->getjson($data);
    }

    /**
     * Editar Video
     *
     * @param Request $request
     * @param $video_identifier
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editVideoAction(Request $request, $identifier = null)
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
            $identity = $helpers->authCheck($hash, true);

            $params = json_decode($json);

            $updatedAt = new \DateTime('now');
            $videoImage = null;
            $videoSource = null;
            $status = (isset($params->status)) ? $params->status : null;;
            $title = (isset($params->title)) ? $params->title : null;
            $description = (isset($params->description)) ? $params->description : null;
            $user_id = ($identity->sub != null) ? $identity->sub : null;

            if ($user_id != null && $title != null) {
                $em = $this->getDoctrine()->getManager();

                $video_repo = $em->getRepository("BackendBundle:Video");
                $video = $video_repo->findOneBy(array(
                    "videoIdentifier" => $identifier
                ));

                // comprueba que el usuario del video y el usuario que edita es el mismo
                if (isset($identity->sub) && $identity->sub == $video->getUser()->getId()) {
                    $video->setTitle($title);
                    $video->setDescription($description);
                    $video->setStatus($status);
                    $video->setUpdatedAt($updatedAt);

                    $em->persist($video);
                    $em->flush();

                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "msg" => "Video update success!!"
                    );
                } else {
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "msg" => "Video not updated, you not owner"
                    );
                }

            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "msg" => "Video not updated"
                );
            }

        } else {
            $data = array(
                "status" => "error",
                "code" => 400,
                "msg" => "Auth or data error"
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
