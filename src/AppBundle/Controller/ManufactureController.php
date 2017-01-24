<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Manufacture;
use AppBundle\Form\ManufactureType;


/**
 * @Route("/manufacture")
 * 
 * Class ManufactureController
 * @package AppBundle\Controller
 */
class ManufactureController extends Controller
{
    /**
     * @Route("/create", name="manufacture_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $manufacture = new Manufacture();
        
        return $this->createEditForm($request, $manufacture);
        
    }

    /**
     * @Route("/edit/{manufacture}", name="manufacture_edit")
     * @param Request $request
     * @param Manufacture $manufacture
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Manufacture $manufacture)
    {
        return $this->createEditForm($request, $manufacture);
    }

    /**
     * @Route("/delete/{manufacture}", name="manufacture_delete")
     * @param Manufacture $manufacture
     * @return RedirectResponse
     */
    public function deleteAction(Manufacture $manufacture)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($manufacture);
        $em->flush();

        $this->addFlash(
            'notice',
            'Your deletes were saved!'
        );

        return $this->redirectToRoute('manufacture_list');
    }

    /**
     * @Route("/list", name="manufacture_list")
     * @Template()
     */
    public function listAction()
    {
        $manufactures = $this->get('doctrine')->getRepository('AppBundle:Manufacture')->findAll();

        return [
            'manufactures' => $manufactures
        ];
    }
    
    public function createEditForm(Request $request, Manufacture $manufacture)
    {
        $form = $this->createForm(ManufactureType::class, $manufacture);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            
            $em->persist($manufacture);
            $em->flush();
            
            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );
            
            return $this->redirectToRoute('manufacture_list');
        }
        
        $template = $request->isXmlHttpRequest() ? 'AppBundle:Manufacture:create_ajax.html.twig' : 'AppBundle:Manufacture:create.html.twig';
        
        return $this->render($template, array(
            'form' => $form->createView(),
        ));
    }
}
