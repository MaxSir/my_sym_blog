<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Car;
use AppBundle\Form\CarType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/car")
 *
 * Class CarController
 * @package AppBundle\Controller
 */
class CarController extends Controller
{
    /**
     * @Route("/create", name="car_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $car = new Car();

        return $this->createEditForm($request, $car);
    }

    /**
     * @Route("/edit/{car}", name="car_edit")
     * @param Request $request
     * @param Car $car
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Car $car)
    {
        return $this->createEditForm($request, $car);
    }

    /**
     * @Route("/delete/{car}", name="car_delete")
     * @param Car $car
     * @return RedirectResponse
     */
    public function deleteAction(Car $car)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($car);
        $em->flush();
        
        $this->addFlash(
            'notice',
            'Your daletes were saved!'
        );
        
        return $this->redirectToRoute('car_list');
    }

    /**
     * @Route("/list", name="car_list")
     * @Template();
     */
    public function listAction()
    {
        $cars = $this->get('doctrine')->getRepository('AppBundle:Car')->findAll();

        return [
            'cars' => $cars
        ];
    }

    public function createEditForm(Request $request, Car $car)
    {
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $car = $form->getData();

            $em = $this->get('doctrine')->getManager();
            $em->persist($car);
            $em->flush();

            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('car_list');
        }
        return $this->render('@App/Car/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}