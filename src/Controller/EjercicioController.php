<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTimeInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;

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
    public function newGameAction(ManagerRegistry $doctrine,
                                  Request $request,
                                  EjercicioRepository $ejercicioRepository,
                                  MailerInterface $mailer): Response
    {
      //controlar que esta la sesión iniciada.
      $this->denyAccessUnlessGranted('ROLE_USER');

        $ejercicio = new Ejercicio();
        $form = $this->createForm(EjercicioType::class, $ejercicio);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() && !is_null($request)){
          //coger imagen y cambiar nombre y guardarla en el repositorio correspondiente
          $file = $form['imagen']->getData();
          $extension = $file->guessExtension();
          if (!$extension) {
            $extension = 'bin';
          }
          $imgName =  rand(1, 99999).'.'.$extension;
          $file->move("img/ejercicios/", $imgName);
          //coger usuario con la sesion iniciada
          $user = $this->getUser();
          $em = $doctrine->getManager();
          //añadir los datos del form al objeto ejercicio
          $ejercicio = $form->getData();
          //cambiar o añadir los datos que sean necesarios del objeto ejercicio
          $ejercicio->setAutor($user);
          $ejercicio->setFechaCreacion(new \DateTime('@'.strtotime('now')));
          $ejercicio->setRevisado(false);
          $ejercicio->setFechaRevision(null);
          $ejercicio->setAceptado(false);
          $ejercicio->setDisponible(false);
          //TODO --> ver como guardo el codigo en un documento o si lo cambio par que suban ellos el documento
          $ejercicio->setDocumento('documento');
          $ejercicio->setImagen($imgName);

            //guardo el objeto ejercicio en la base de datos
           $em->persist($ejercicio);
           $em->flush();
           //TODO -> mandar correo al admin
           /*$email = (new Email())
                      ->from('asinsanna@gmail.com')
                      ->to('aasins97@gmail.com')
                      ->subject('Time for Symfony Mailer!')
                      ->text('Sending emails is fun again!');
           $mailer->send($email);*/
           //redirijo al usuario a la pagina de ejercicios

           //TODO --> pensar a donde redirijo al usuario.
           return $this->redirectToRoute('games_page');
        }
        return $this->renderForm('ejercicio/anyadir.html.twig', ['form'=>$form]);
    }

    #[Route('/misejercicios', name: 'myGames_page')]
    public function myGamesAction(EjercicioRepository $ejercicioRepository): Response
    {
      $this->denyAccessUnlessGranted('ROLE_USER');

        $ejerciciosDisponibles = $ejercicioRepository->findBy(array('autor' => $this->getUser(), 'aceptado' => true, 'disponible' => true));
        $ejerciciosNoDisponibles = $ejercicioRepository->findBy(array('autor' => $this->getUser(), 'aceptado' => true, 'disponible' => false));
        $ejerciciosDenegados = $ejercicioRepository->findBy(array('autor' => $this->getUser(), 'aceptado' => false, 'revisado' => true));
        $ejerciciosR = $ejercicioRepository->findBy(array('autor' => $this->getUser(), 'revisado' => false));
        return $this->render('ejercicio/misEjercicios.html.twig', array('ejerciciosD'=>$ejerciciosDisponibles,
                                                                        'ejerciciosN' => $ejerciciosNoDisponibles,
                                                                        'ejerciciosDen' => $ejerciciosDenegados,
                                                                        'ejerciciosR' => $ejerciciosR
                                                                        ));
    }

    #[Route('/cambiarDisponible/{id}', name: 'app_cambiarDisponible')]
    public function cambiarDisponibleAction(int $id, EntityManagerInterface $entityManager): Response
    {
      $ejercicio = $entityManager->getRepository(Ejercicio::class)->find($id);
      $disponible = $ejercicio -> getDisponible();
      $ejercicio -> setDisponible(!$disponible);
      $entityManager->persist($ejercicio);
      $entityManager->flush();
      return $this->redirectToRoute('myGames_page');
    }
}
