<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Ejercicio;
use App\Repository\EjercicioRepository;

class DefaultController extends AbstractController
{
   #[Route('/', name: 'home_page')]
    public function homeAction(EjercicioRepository $ejercicioRepository): Response
    {
      //Capturar el repositorio de la tabla contra la bd
      $ejercicios = $ejercicioRepository->findThreeToShow();
      return $this->render('frontal/index.html.twig', array('ejercicios'=>$ejercicios));
    }

     #[Route('/contact', name: 'contact_page')]
    public function contactAction(): Response
    {
        return $this->render('frontal/contact.html.twig');
    }

    #[Route('/prueba', name: 'prueba_page')]
   public function pruebaAction(): Response
   {
       return $this->render('prueba/prueba.html.twig');
   }
}

 ?>
