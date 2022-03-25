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
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

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

    #[Route('/revisarejercicios', name: 'gamesReview_page')]
    public function gamesReviewAction(EjercicioRepository $ejercicioRepository): Response
    {
      $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $ejerciciosAceptados = $ejercicioRepository->findBy(array('aceptado' => true, 'revisado' => true));
        $ejerciciosDenegados = $ejercicioRepository->findBy(array('aceptado' => false, 'revisado' => true));
        $ejerciciosR = $ejercicioRepository->findBy(array('revisado' => false));
        return $this->render('ejercicio/revisarEjercicios.html.twig', array('ejerciciosA' => $ejerciciosAceptados,
                                                                        'ejerciciosD' => $ejerciciosDenegados,
                                                                        'ejerciciosR' => $ejerciciosR
                                                                        ));
    }

    #[Route('/aceptarejercicio/{id}', name: 'app_aceptarEjercicio')]
    public function aceptarEjercicioAction(int $id, EntityManagerInterface $entityManager): Response
    {
      $ejercicio = $entityManager->getRepository(Ejercicio::class)->find($id);
      $aceptado = $ejercicio -> getAceptado();
      $ejercicio -> setAceptado(true);
      $ejercicio -> setRevisado(true);
      $ejercicio -> setDisponible(true);
      $entityManager->persist($ejercicio);
      $entityManager->flush();
      return $this->redirectToRoute('gamesReview_page');
    }
    #[Route('/denegarejercicio/{id}', name: 'app_denegarEjercicio')]
    public function denegarEjercicioAction(int $id, EntityManagerInterface $entityManager): Response
    {
      $ejercicio = $entityManager->getRepository(Ejercicio::class)->find($id);
      $aceptado = $ejercicio -> getAceptado();
      $ejercicio -> setAceptado(false);
      $ejercicio -> setRevisado(true);
      $entityManager->persist($ejercicio);
      $entityManager->flush();
      return $this->redirectToRoute('gamesReview_page');
    }

    #[Route('/descargarimagen/{id}', name: 'app_descargarImagen')]
    public function descargarImagenAction(int $id, EntityManagerInterface $entityManager): Response
    {
      /*$ejercicio = $entityManager->getRepository(Ejercicio::class)->find($id);
      $imagen = $ejercicio -> getImagen();
      $imagenPath = "img/ejercicios/".$imagen;
      */
      $finder = new Finder();
      $fs = new Filesystem();
      // find all files in the current directory
      $finder->files()->in("img/ejercicios");
      $zip = new \ZipArchive();
      $zip->open("prueba.zip", \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
      foreach ($finder as $file) {
          $absoluteFilePath = $file->getRealPath();
          $fileNameWithExtension = $file->getRelativePathname();
          $zip->addFile($absoluteFilePath, $fileNameWithExtension);
      }
      $zip->close();

      //descargar Zip
      if(file_exists("prueba.zip")){
        // Define headers
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=prueba.zip");
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");
        // Read the file
        readfile("prueba.zip");
        $fs->remove("prueba.zip");

        exit;
      }else{
          echo 'The file does not exist.';
      }
      //return $this->redirectToRoute('gamesReview_page');
      return $this->render('prueba/prueba.html.twig');
    }
}
