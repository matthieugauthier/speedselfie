<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PostsController extends Controller
{
    /**
     * @Route("/gallery", name="gallery")
     */
    public function galleryAction(Request $request)
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findClose(50);

        return $this->render('default/gallery.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/api/mosaic", name="mosaic")
     */
    public function mosaicAction(Request $request)
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findClose();

        $o = [];
        foreach ($posts as $post) {
            $o[] = [
                'id' => $post->getId(),
                'question'=> $post->getQuestion(),
                'response' => $post->getResponse(),
                'url' => $post->getPhoto()
            ];

        }

        return new JsonResponse($o);
    }
}
