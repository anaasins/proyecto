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
      $user= $this->getUser();
      $ejercicio = $entityManager -> getRepository(Ejercicio::class)->findOneBy(array('id'=>$id));
      if ($user) {
        $entrenamientoRepository = $entityManager -> getRepository(Entrenamiento::class);
        $entrenamiento = $entrenamientoRepository->findOneBy(array('usuario'=>$user->getId(), 'ejercicio'=>$id), array('id'=>'DESC'));
        return $this->render('ejercicios_disponibles/'.$id.'/index.html.twig', array('entrenamiento' => $entrenamiento,
                                                                                      'ejercicio' => $ejercicio
                                                                                    ));
      }else {
        return $this->render('ejercicios_disponibles/'.$id.'/index.html.twig', array('ejercicio' => $ejercicio
                                                                                    ));
      }
  }

  #[Route('/guardarEntrenamiento', name: 'app_guardarEntrenamiento')]
  public function guardarEntrenamientoAction(EntityManagerInterface $entityManager): Response
  {
    $user= $this->getUser();
    if ($user) {
      $entrenamiento = new Entrenamiento();
      $ejercicio = $entityManager -> getRepository(Ejercicio::class)->findOneBy(array('id'=>$_POST['ejercicio']));

      $entrenamiento -> setUsuario($user);
      $entrenamiento -> setEjercicio($ejercicio);
      $entrenamiento -> setFecha(new \DateTime('@'.strtotime('now')));
      $entrenamiento -> setPuntuacion($_POST['puntos']);
      $entrenamiento -> setNivelAlcanzado($_POST['nivel']);
      $entityManager->persist($entrenamiento);
      $entityManager->flush();
    }
    return $this->redirectToRoute('games_page');
  }

  #[Route('/graficas/{id}', name: 'graficas_page')]
  public function graficasAction(int $id=0, EntityManagerInterface $entityManager): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');

      $user= $this->getUser();
      $entrenamientoRepository = $entityManager -> getRepository(Entrenamiento::class);
      $entrenamientos = $entrenamientoRepository->findBy(array('usuario' => $user->getId() ), array('id'=>'ASC'));
      $ejercicios = array();
      for ($i=0; $i < count($entrenamientos); $i++) {
        if (!in_array($entrenamientos[$i]->getEjercicio(), $ejercicios)) {
          array_push($ejercicios, $entrenamientos[$i]->getEjercicio());
        }
      }
      $fechas = array();
      $puntuaciones = array();
      $niveles = array();
      $datos=null;
      if ($id!=0) {
        $datos = $entrenamientoRepository -> findBy(array('usuario' => $user->getId(), 'ejercicio' => $id), array('id'=>'ASC'));
        for ($i=0; $i < count($datos); $i++) {
          array_push($fechas, $datos[$i]->getFecha()->format('d-m-Y'));
          array_push($puntuaciones, $datos[$i]->getPuntuacion());
          array_push($niveles, $datos[$i]->getNivelAlcanzado());
        }
      }

      return $this->render('entrenamiento/graficas.html.twig', array('ejercicios' => $ejercicios,
                                                                      'datos' => $datos,
                                                                      'fechas' => $fechas,
                                                                      'puntuaciones' => $puntuaciones,
                                                                      'niveles' => $niveles
                                                                    ));
      //return $this->render('prueba/prueba.html.twig');
  }
}
