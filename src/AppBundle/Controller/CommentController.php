<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BackendBundle\Entity\User;
use BackendBundle\Entity\Video;
use BackendBundle\Entity\Comment;

class CommentController extends Controller
{

    /**
     * Crear nuevo Comentario
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newCommentAction(Request $request)
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
            $user_id = ($identity->sub != null) ? $identity->sub : null;
            $video_id = (isset($params->video_id)) ? $params->video_id : null;
            $body = (isset($params->body)) ? $params->body : null;


            if ($user_id != null && $video_id != null) {
                $em = $this->getDoctrine()->getManager();

                $user_repo = $em->getRepository('BackendBundle:User');
                $user = $user_repo->findOneBy(array(
                    "id" => $user_id
                ));

                $video_repo = $em->getRepository('BackendBundle:Video');
                $video = $video_repo->findOneBy(array(
                    "id" => $video_id
                ));

                $comment = new Comment();
                $comment->setUser($user);
                $comment->setBody($body);
                $comment->setCreatedAt($createdAt);
                $comment->setVideo($video);

                $em->persist($comment);
                $em->flush();

                $data = array(
                    "status" => "success",
                    "code" => 200,
                    "msg" => "Comment created"
                );

            } else {
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "msg" => "Comment not created"
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
     * Borrar comentario
     *
     * @param Request $request
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCommentAction(Request $request, $id = null)
    {
        $helpers = $this->get("app.apirest.helpers");

        $hash = $request->get("auth", null);

        if ($hash != null) {
            $authCheck = $helpers->authCheck($hash);
        } else {
            $authCheck = false;
        }

        if ($authCheck) {
            $identity = $helpers->authCheck($hash, true);

            $user_id = ($identity->sub != null) ? $identity->sub : null;

            $em = $this->getDoctrine()->getManager();

            $comment_repo = $em->getRepository('BackendBundle:Comment');
            $comment = $comment_repo->findOneBy(array(
                "id" => $id
            ));

            // default
            $data = array(
                "status" => "error",
                "code" => 400,
                "msg" => "Comment not deleted"
            );

            if (is_object($comment) && $user_id != null && isset($identity->sub)) {

                $is_owner_comment = ($identity->sub == $comment->getUser()->getId()) ? true : false;
                $is_owner_video = ($identity->sub == $comment->getVideo()->getUser()->getId()) ? true : false;

                if ( $is_owner_comment || $is_owner_video ) {

                    $em->remove($comment);
                    $em->flush();

                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "msg" => "Comment deleted"
                    );

                }
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
     * Listado de comentarios para un video
     *
     * @param Request $request
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCommentAction(Request $request, $id = null)
    {
        $helpers = $this->get("app.apirest.helpers");

        $em = $this->getDoctrine()->getManager();

        $video_repo = $em->getRepository('BackendBundle:Video');
        $video = $video_repo->findOneBy(array(
            "id" => $id
        ));

        $comment_repo = $em->getRepository('BackendBundle:Comment');
        $comments = $comment_repo->findBy(array(
            "video" => $video
        ), array('id'=>'DESC'));

        if (count($comments) >= 1) {

            $data = array(
                "status" => "success",
                "code" => 200,
                "data" => $comments
            );

        } else {

            $data = array(
                "msg" => "There are no comments for this video"
            );
        }

        return $helpers->getjson($data);
    }
    
}
