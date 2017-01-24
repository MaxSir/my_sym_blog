<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Model;
use AppBundle\Form\ModelType;

/**
 * @Route("/model")
 *
 * Class ModelController
 * @package AppBundle\Controller
 */
class ModelController extends Controller
{
    /**
     * @Route("/create", name="model_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $model = new Model();

        return $this->createEditForm($request, $model);
    }

    /**
     * @Route("/edit/{model}", name="model_edit")
     * @param Request $request
     * @param Model $model
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Model $model)
    {
        return $this->createEditForm($request, $model);
    }

    /**
     * @Route("/delete/{model}", name="model_delete")
     * @param Model $model
     * @return RedirectResponse
     */
    public function deleteAction(Model $model)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($model);
        $em->flush();

        $this->addFlash(
            'notice',
            'Your deletes were saved!'
        );

        return $this->redirectToRoute('model_list');
    }

    /**
     * @Route("/list", name="model_list")
     * @Template()
     */
    public function listAction()
    {
        $models = $this->get('doctrine')->getRepository('AppBundle:Model')->findAll();

        return [
            'models' => $models
        ];
    }
    
    public function createEditForm(Request $request, Model $model)
    {
        $form = $this->createForm(ModelType::class, $model);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $model = $form->getData();
            
            $em = $this->get('doctrine')->getManager();
            $em->persist($model);
            $em->flush();
            
            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );
            
            return $this->redirectToRoute('model_list');
        }
        
        $template = $request->isXmlHttpRequest() ? 'AppBundle:Model:create_ajax.html.twig' : 'AppBundle:Model:create.html.twig';
        
        return $this->render($template, array(
            'form' => $form->createView(),
        ));
    }
}