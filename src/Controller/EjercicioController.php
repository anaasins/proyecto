<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTimeInterface;
use Symfony\Component\Validator\Constraints\DateTime;

use App\Form\EjercicioType;
use App\Entity\Ejercicio;
use App\Entity\Usuario;
use App\Repository\EjercicioRepository;
use App\Repository\UsuarioRepository;

class EjercicioController extends AbstractController
{
    #[Route('/games', name: 'games_page')]
    public function gamesAction(EjercicioRepository $ejercicioRepository): Response
    {
        $ejercicios = $ejercicioRepository->findAllToShow();
        return $this->render('ejercicio/index.html.twig', array('ejercicios'=>$ejercicios));
    }

    #[Route('/newGame', name: 'newGame_page')]
    public function newGameAction(ManagerRegistry $doctrine, Request $request, EjercicioRepository $ejercicioRepository, UsuarioRepository $usuarioRepository): Response
    {

        $ejercicio = new Ejercicio();
      /*  $formBuilder = $this->createFormBuilder($ejercicio)
                            ->add('nombre', TextType::class)
                            ->add('descripcion', TextareaType::class)
                            ->add('niveles_disponibles', IntegerType::class)
                            ->add('enviar', SubmitType::class, ['label' => 'Añadir Ejercicio']);
        $form = $formBuilder->getForm();*/
        $form = $this->createForm(EjercicioType::class, $ejercicio);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() && !is_null($request)){
          /*$datos = $request->request->all();
          $file = $form['imagen']->getData();
          $file->move("img/ejercicios/", "nueva.png");*/
          $user = $usuarioRepository->findById(1);
          $em = $doctrine->getManager();
          $ejercicio = $form->getData();
          //$ejercicio->setNombre($form['nombre']->getData());
          //$ejercicio->setDescripcion($form['descripcion']->getData());
          $ejercicio->setAutor($user[0]);
          $ejercicio->setFechaCreacion(new \DateTime('@'.strtotime('now')));
          $ejercicio->setRevisado(false);
          $ejercicio->setFechaRevision(null);
          $ejercicio->setAceptado(false);
          $ejercicio->setDisponible(false);
          $ejercicio->setDocumento('documento');
          $ejercicio->setImagen('imagen');
          //$ejercicio->setNivelesDisponibles(2);

           $em->persist($ejercicio);
           $em->flush();

           return $this->redirectToRoute('games_page');
        }
        return $this->renderForm('ejercicio/anyadir.html.twig', ['form'=>$form,]);
    }
}
