<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $myUser = $user = $this->getUser();
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findMates($user);

        $list = unserialize($this->get('session')->get('list'));

        if (!$list) {
            $list = [
                'one1' => null,
                'one2' => null,
                'two' => null,
                'three' => null,
                'five' => null,
            ];
        }

        $types = $this->getDoctrine()->getRepository('AppBundle:Post')->findOpenType(1, $myUser);
        if (count($types) === 2) {
            $list['one1'] = $types[0];
            $list['one2'] = $types[1];
        }
        if (count($types) === 1) {
            $list['one1'] = $types[0];
            try {
                $p = $this->get('pop')->tryToFindPost(1, $myUser, $users);

                $this->getDoctrine()->getManager()->persist($p);
                $this->getDoctrine()->getManager()->flush();
                $list['one2'] = $p;
            } catch (\Exception $e) {
                if ($e->getCode() === 8000) {

                } elseif ($e->getCode() === 8001) {

                } else {
                    throw $e;
                }
            }
        }
        if (count($types) === 0) {
            try {
                $p = $this->get('pop')->tryToFindPost(1, $myUser, $users);

                $this->getDoctrine()->getManager()->persist($p);
                $this->getDoctrine()->getManager()->flush();
                $list['one1'] = $p;
            } catch (\Exception $e) {
                if ($e->getCode() === 8000) {

                } elseif ($e->getCode() === 8001) {

                } else {
                    throw $e;
                }
            }
            try {
                $p2 = $this->get('pop')->tryToFindPost(1, $myUser, $users);

                $this->getDoctrine()->getManager()->persist($p2);
                $this->getDoctrine()->getManager()->flush();
                $list['one2'] = $p2;
            } catch (\Exception $e) {
                if ($e->getCode() === 8000) {

                } elseif ($e->getCode() === 8001) {

                } else {
                    throw $e;
                }
            }
        }

        $a = ['two' => 2, 'three' => 3, 'five' => 5];
        foreach ($a as $type => $number) {
            try {
                $list[$type] = $this->get('pop')->getPost($number, $myUser, $users);
                if ($list[$type] !== null) {
                    $this->getDoctrine()->getManager()->persist($list[$type]);
                }
            } catch (\Exception $e) {
                if ($e->getCode() === 8000) {

                } elseif ($e->getCode() === 8001) {

                } else {
                    throw $e;
                }
            }
        }
        $this->getDoctrine()->getManager()->flush();

        // dump($list);

        return $this->render('default/index.html.twig', [
            'list' => $list
        ]);
    }

    /**
     * @Route("/take/{postId}", name="take")
     */
    public function takeAction($postId, Request $request)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($postId);

        if (!$post || $myUser->getId() != $post->getAuthor()->getId() || $post->getPhoto()) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $form = $this->createFormBuilder()
            ->add('file', FileType::class, [
                'label' => 'Selfie',
                'attr' => [
                    'accept' => "image/*",
                    'class' => "inputfile hidden"
                ]
            ])
            ->add('response', TextType::class, array(
                'label' => $post->getQuestion(),
                'attr' => [
                    'class' => "form-control"
                ]
            ))
            ->add('send', SubmitType::class, array(
                'label' => 'Envoyer',
                'attr' => [
                    'class' => "btn btn-primary btn-lg btn-block"
                ]
            ))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fc = file_get_contents($form->getData()['file']->getRealPath());
            $ext = explode('.', $form->getData()['file']->getClientOriginalName());
            $ext = $ext[count($ext) - 1];
            file_put_contents('upload/' . $post->getId() . '.' . $ext, $fc);

            $post->setPhoto('upload/' . $post->getId() . '.' . $ext);
            $post->setDatetime(new \DateTime());
            $post->setResponse($form->getData()['response']);

            $this->getDoctrine()->getManager()->persist($post);
            $this->getDoctrine()->getManager()->flush();

            $myUser = $this->getDoctrine()->getRepository('AppBundle:User')->findOneById($myUser);
            $s = $myUser->getScore();
            $s += $post->getType();

            $myUser->setScore($s);
            $this->getDoctrine()->getManager()->persist($myUser);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('default/take.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }



    /*
     * @Route("/logout", name="logout")
     *
    public function logoutAction(Request $request)
    {

        return $this->redirect('homepage');
    }*/

    /**
     * @Route("/score", name="score")
     */
    public function scoreAction(Request $request)
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findBy([], ['score' => 'desc']);

        return $this->render('default/score.html.twig', [
            'users' => $users
        ]);
    }
}
