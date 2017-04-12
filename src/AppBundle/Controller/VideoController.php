<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BackendBundle\Entity\User;
use BackendBundle\Entity\Video;

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
     * @param $identifier
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
            $status = (isset($params->status)) ? $params->status : null;
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
     * Carga de archivos para video
     *
     * @param Request $request
     * @param $identifier
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadAction(Request $request, $identifier = null)
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
            $video_repo = $em->getRepository('BackendBundle:Video');
            $video = $video_repo->findOneBy(array(
                "videoIdentifier" => $identifier
            ));

            $is_owner = (isset($identity->sub) && $identity->sub == $video->getUser()->getId()) ? true : false;

            // files
            $video_source = $request->files->get("videoSource");
            $video_image = $request->files->get("videoImage");

            $user_media_route = 'uploads/media/'.$video->getUser()->getUserIdentifier().'_usermedia/videos';

            if (!empty($video_source) && $video_source != null && $is_owner) {
                $ext = $video_source->guessExtension(); // obtencion de extension
                if ($ext == 'mp4') {
                    $file_name = $video->getVideoIdentifier().'_vid_'.time().'.'.$ext;
                    $video_source->move($user_media_route, $file_name);

                    $video->setVideoSource($file_name);
                    $em->persist($video);
                    $em->flush();

                    $data[] = "Video uploaded";
                } else {
                    $data[] = "Video format not valid, only mp4";
                }
            }

            if (!empty($video_image) && $video_image != null && $is_owner) {
                $ext = $video_image->guessExtension(); // obtencion de extension
                if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') {
                    $file_name = $video->getVideoIdentifier().'_imgvid_'.time().'.'.$ext;
                    $video_image->move($user_media_route, $file_name);

                    $video->setVideoImage($file_name);
                    $em->persist($video);
                    $em->flush();

                    $data[] = "Image for video uploaded";
                } else {
                    $data[] = "Image format not valid";
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

    /**
     * Listado de videos
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listVideoAction(Request $request)
    {
        $helpers = $this->get("app.apirest.helpers");

        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT v FROM BackendBundle:Video v ORDER BY v.id DESC";
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $actual_page = $request->query->getInt('page', 1); // parametro request de paginacion y en que num de pagina empieza
        $items_per_page = 5;
        $pagination = $paginator->paginate(
            $query,
            $actual_page,
            $items_per_page
        );
        $video_items_count = $pagination->getTotalItemCount();

        $data = array(
            "status" => "success",
            "video_items_count" => $video_items_count,
            "actual_page" => $actual_page,
            "items_per_page" => $items_per_page,
            "total_pages" => ceil($video_items_count / $items_per_page),
            "data" => $pagination
        );

        return $helpers->getjson($data);
    }

    /**
     * Videos recientes
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lastsVideosAction(Request $request)
    {
        $helpers = $this->get("app.apirest.helpers");

        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT v FROM BackendBundle:Video v ORDER BY v.createdAt DESC";
        $query = $em->createQuery($dql)->setMaxResults(5);
        $videos = $query->getResult();

        $data = array(
            "status" => "success",
            "data" => $videos
        );

        return $helpers->getjson($data);
    }

    /**
     * Video en detalle
     *
     * @param Request $request
     * @param null $identifier
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailVideoAction(Request $request, $identifier = null)
    {
        $helpers = $this->get("app.apirest.helpers");

        $em = $this->getDoctrine()->getManager();

        $video_repo = $em->getRepository('BackendBundle:Video');
        $video = $video_repo->findOneBy(array(
            "videoIdentifier" => $identifier
        ));

        if ($video) {
            $data = array(
                "status" => "success",
                "data" => $video
            );
        } else {
            $data[] = array(
                "status" => "error",
                "code" => 400,
                "msg" => "Video don't exists"
            );
        }

        return $helpers->getjson($data);
    }

    /**
     * Busqueda de video
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request, $keyword = null)
    {
        $helpers = $this->get("app.apirest.helpers");

        $em = $this->getDoctrine()->getManager();

        if($keyword != null) {
            $dql = "SELECT v FROM BackendBundle:Video v "
            . "WHERE v.title LIKE :keyword OR v.description LIKE :keyword ORDER BY v.id DESC";
            $query = $em->createQuery($dql)->setParameter("keyword", "%$keyword%");
        } else {
            $dql = "SELECT v FROM BackendBundle:Video v ORDER BY v.id DESC";
            $query = $em->createQuery($dql);
        }

        $paginator = $this->get('knp_paginator');
        $actual_page = $request->query->getInt('page', 1); // parametro request de paginacion y en que num de pagina empieza
        $items_per_page = 5;
        $pagination = $paginator->paginate(
            $query,
            $actual_page,
            $items_per_page
        );
        $video_items_count = $pagination->getTotalItemCount();

        $data = array(
            "status" => "success",
            "video_items_count" => $video_items_count,
            "actual_page" => $actual_page,
            "items_per_page" => $items_per_page,
            "total_pages" => ceil($video_items_count / $items_per_page),
            "data" => $pagination
        );

        return $helpers->getjson($data);
    }
    
}
