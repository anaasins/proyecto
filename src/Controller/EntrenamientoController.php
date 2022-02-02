<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Entrenamiento;
use App\Repository\EntrenamientoRepository;

class EntrenamientoController extends AbstractController
{
    #[Route('/games', name: 'games_page')]
    public function gamesAction(EntrenamientoRepository $entrenamientoRepository): Response
    {
      //Capturar el repositorio de la tabla contra la bd
      $entrenamientos = $entrenamientoRepository->findAllToShow();
      return $this->render('frontal/entrenamientos.html.twig', array('entrenamientos'=>$entrenamientos));
    }
}
