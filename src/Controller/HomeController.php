<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Poll;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()

    {   
        $entityManager = $this->getDoctrine()->getManager();   
        $polls=$entityManager->getRepository(Poll::class)->findBy(array(), array('creationDate' => 'DESC'),8);
        return $this->render('home/index.html.twig',[
            'polls' => $polls,
        ]);
            

    }
}
