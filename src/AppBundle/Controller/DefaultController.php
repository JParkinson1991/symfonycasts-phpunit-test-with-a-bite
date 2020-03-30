<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Enclosure;
use AppBundle\Factory\DinosaurFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /* @var \AppBundle\Entity\Enclosure[] $enclosures */
        $enclosures = $this->getDoctrine()
            ->getRepository(Enclosure::class)
            ->findAll();

        return $this->render('default/index.html.twig', [
            'enclosures' => $enclosures
        ]);
    }

    /**
     * 'Adds' a dinosaur to an enclosure
     *
     * @Route("/grow", name="grow_dinosaur")
     * @Method({"POST"})
     *
     * @throws \Exception
     */
    public function growAction(Request $request, DinosaurFactory $dinosaurFactory)
    {
        $manager = $this->getDoctrine()->getManager();

        /** @var Enclosure $enclosure */
        $enclosure = $manager->getRepository(Enclosure::class)
            ->find($request->request->get('enclosure'));

        $specification = $request->request->get('specification');
        $dinosaur = $dinosaurFactory->growFromSpecification($specification);

        $dinosaur->setEnclosure($enclosure);
        $enclosure->addDinosaur($dinosaur);

        $manager->flush();

        $this->addFlash('success', sprintf(
            'Grew a %s in enclosure #%d',
            mb_strtolower($specification),
            $enclosure->getId()
        ));

        return $this->redirectToRoute('homepage');
    }
}
