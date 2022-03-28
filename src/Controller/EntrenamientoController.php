<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Entrenamiento;
use App\Entity\Usuario;
use App\Entity\Ejercicio;
use App\Repository\EntrenamientoRepository;
use Doctrine\ORM\EntityManagerInterface;


class EntrenamientoController extends AbstractController
{
  #[Route('/entrenar/{id}', name: 'app_entrenar')]
  public function entrenarAction(int $id, EntityManagerInterface $entityManager): Response
  {
      $user= $this->getUser();
      $entrenamientoRepository = $entityManager -> getRepository(Entrenamiento::class);
      $entrenamiento = $entrenamientoRepository->findOneBy(array('usuario'=>$user->getId(), 'ejercicio'=>$id), array('id'=>'DESC'));
      $ejercicio = $entityManager -> getRepository(Ejercicio::class)->findOneBy(array('id'=>$id));
      return $this->render('ejercicios_disponibles/'.$id.'/index.html.twig', array('entrenamiento' => $entrenamiento,
                                                                                    'ejercicio' => $ejercicio
                                                                                  ));
  }

  #[Route('/guardarEntrenamiento', name: 'app_guardarEntrenamiento')]
  public function guardarEntrenamientoAction(): Response
  {

  }
}
