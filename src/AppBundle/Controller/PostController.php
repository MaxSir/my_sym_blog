<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/blog/post")
 * Class PostController
 * @package AppBundle\Controller
 */
class PostController extends Controller
{
    /**
     * @Route("/create", name="post_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAction (Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setDateTime(new \DateTime());

            $em = $this->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('homepage');
        }
        return $this->render('@App/Post/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="post_list")
     * @Template()
     */
    public function postListAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $post = $em->getRepository('AppBundle:Post')->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        return $this->render('AppBundle:Post:postList.html.twig', array(
            'post'      => $post,
        ));
    }
}
