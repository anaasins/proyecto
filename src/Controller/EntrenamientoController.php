<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Entrenamiento;
use App\Repository\EntrenamientoRepository;

class EntrenamientoController extends AbstractController
{
  #[Route('/entrenar/{id}', name: 'app_entrenar')]
  public function entrenarAction(int $id): Response
  {
      return $this->render('ejercicios_disponibles/'.$id.'/index.html.twig');
  }
}
