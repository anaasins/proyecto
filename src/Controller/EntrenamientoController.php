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
  #[Route('/entrenar/{id}', name: 'entrenar_page')]
  public function entrenarAction(int $id, EntityManagerInterface $entityManager): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');

      $user= $this->getUser();
      $entrenamientoRepository = $entityManager -> getRepository(Entrenamiento::class);
      $entrenamiento = $entrenamientoRepository->findOneBy(array('usuario'=>$user->getId(), 'ejercicio'=>$id), array('id'=>'DESC'));
      $ejercicio = $entityManager -> getRepository(Ejercicio::class)->findOneBy(array('id'=>$id));
      return $this->render('ejercicios_disponibles/'.$id.'/index.html.twig', array('entrenamiento' => $entrenamiento,
                                                                                    'ejercicio' => $ejercicio
                                                                                  ));
  }

  #[Route('/guardarEntrenamiento', name: 'app_guardarEntrenamiento')]
  public function guardarEntrenamientoAction(EntityManagerInterface $entityManager): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');

    $entrenamiento = new Entrenamiento();
    $user= $this->getUser();
    $ejercicio = $entityManager -> getRepository(Ejercicio::class)->findOneBy(array('id'=>$_POST['ejercicio']));

    $entrenamiento -> setUsuario($user);
    $entrenamiento -> setEjercicio($ejercicio);
    $entrenamiento -> setFecha(new \DateTime('@'.strtotime('now')));
    $entrenamiento -> setPuntuacion($_POST['puntos']);
    $entrenamiento -> setNivelAlcanzado($_POST['nivel']);
    $entityManager->persist($entrenamiento);
    $entityManager->flush();

    return $this->redirectToRoute('games_page');
  }

  #[Route('/graficas', name: 'graficas_page')]
  public function graficasAction(EntityManagerInterface $entityManager): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');

      $user= $this->getUser();
      $entrenamientoRepository = $entityManager -> getRepository(Entrenamiento::class);
      $entrenamiento = $entrenamientoRepository->findBy(array('usuario' => $user->getId() ), array('id'=>'ASC'));
      // TODO --> Hacer un array de nombres de ejercicios comprobando antes que no esta.
      $ejercicios = array();
      for ($i=0; $i < count($entrenamiento); $i++) {
        if (!in_array($entrenamiento[$i]->getEjercicio(), $ejercicios)) {
          array_push($ejercicios, $entrenamiento[$i]->getEjercicio());
        }
      }
//      var_dump($entrenamiento);
      //$ejercicio = $entityManager -> getRepository(Ejercicio::class)->findOneBy(array('id'=>$id));
      return $this->render('entrenamiento/graficas.html.twig', array('entrenamiento' => $ejercicios));
      //return $this->render('prueba/prueba.html.twig');
  }
}
