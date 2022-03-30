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
    public function newGameAction(EntityManagerInterface $entityManager,
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
          // me guardo el id del ejercicio
          $rand = rand(1, 99999);
          //coger imagen y cambiar nombre y guardarla en el repositorio correspondiente
          $file = $form['imagen']->getData();
          $imgextension = $file->guessExtension();
          if (!$imgextension) {
            $imgextension = 'bin';
          }
          $imgName =  $rand.'.'.$imgextension;
          $file->move("img/ejercicios/", $imgName);
          //Coger el documento de codigo
          $filesystem = new Filesystem();
          $file = $form['documento']->getData();
          $extension = $file->guessExtension();
          if ($extension=='html') {
            $fileName = 'index.html.twig';
            //creo la carpeta donde debe ir el ejercicio, si no existe
            if(!$filesystem->exists('../templates/ejercicios_disponibles/'.$rand)){
              $filesystem -> mkdir('../templates/ejercicios_disponibles/'.$rand);
            }
            $file -> move('../templates/ejercicios_disponibles/'.$rand.'/', $fileName);
          }

          //Procesar los documentos extra.
          $extras = $form['documentosExtra']->getData();
          $extrasD =array();
          for ($i=0; $i < count($extras); $i++) {
            $file = $extras[$i];
            $fileName = $file->getClientOriginalName();
            if(!$filesystem->exists('extras_ejercicios/'.$rand)){
              $filesystem -> mkdir('extras_ejercicios/'.$rand);
            }
            $file -> move('extras_ejercicios/'.$rand.'/', $fileName);
            array_push($extrasD, $fileName);
          }

          $ejercicio -> setDocumentosExtra($extrasD);

          //coger usuario con la sesion iniciada
          $user = $this->getUser();
          //añadir los datos del form al objeto ejercicio
          //$ejercicio = $form->getData();
          //cambiar o añadir los datos que sean necesarios del objeto ejercicio
          $ejercicio->setAutor($user);
          $ejercicio->setFechaCreacion(new \DateTime('@'.strtotime('now')));
          $ejercicio->setRevisado(false);
          $ejercicio->setFechaRevision(null);
          $ejercicio->setAceptado(false);
          $ejercicio->setDisponible(false);
          $ejercicio->setDocumento('documento');
          $ejercicio->setImagen('img');
          if ($form['niveles_disponibles']->getData() == null ||  $form['niveles_disponibles']->getData() == '' ) {
            $ejercicio->setNivelesDisponibles(0);
          }
            //guardo el objeto ejercicio en la base de datos
          $entityManager->persist($ejercicio);
          $entityManager->flush();
          //recojo el id del ejercicio que acabo de crear
          $id = $ejercicio -> getId();
          //cambio los nombres de la carpeta de src y de la imagen del ejercicio
          if ($filesystem -> exists("img/ejercicios/".$imgName)){
            $filesystem -> rename("img/ejercicios/".$imgName, "img/ejercicios/".$id.".".$imgextension);
            $ejercicio->setImagen($id.".".$imgextension);
          }
          if ($filesystem -> exists('../templates/ejercicios_disponibles/'.$rand)){
            $filesystem -> rename('../templates/ejercicios_disponibles/'.$rand, '../templates/ejercicios_disponibles/'.$id);
            $ejercicio->setDocumento('/ejercicios_disponibles/'.$id);
          }
          if ($filesystem -> exists('extras_ejercicios/'.$rand)){
            $filesystem -> rename('extras_ejercicios/'.$rand, 'extras_ejercicios/'.$id);
          }
          //actualizo con los nuevos nombres de imagen y archivo
          $entityManager->persist($ejercicio);
          $entityManager->flush();

          //TODO -> mandar correo al admin
           /*$email = (new Email())
                      ->from('asinsanna@gmail.com')
                      ->to('aasins97@gmail.com')
                      ->subject('Time for Symfony Mailer!')
                      ->text('Sending emails is fun again!');
           $mailer->send($email);*/
           //redirijo al usuario a la pagina de ejercicios

          //return $this->render('prueba/prueba.html.twig');
          return $this->redirectToRoute('myGames_page');
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

    #[Route('/revisar', name: 'gamesReview_page')]
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

    #[Route('/revisarEjercicio/{id}', name: 'app_revisarEjercicio')]
    public function revisarEjercicioAction(int $id, EntityManagerInterface $entityManager): Response
    {
      /*$ejercicio = $entityManager->getRepository(Ejercicio::class)->find($id);
      $imagen = $ejercicio -> getImagen();
      $imagenPath = "img/ejercicios/".$imagen;
      */
      $zipName = $id.".zip";
      $finder = new Finder();
      $fs = new Filesystem();
      // find all files in the current directory
      $zip = new \ZipArchive();
      $zip->open($zipName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
      if ($fs -> exists("../templates/ejercicios_disponibles/".$id)) {
        $finder->files()->in("../templates/ejercicios_disponibles/".$id);
        //descargo codigo
        if (!empty($finder)) {
          $zip -> addEmptyDir("codigo");
          foreach ($finder as $file) {
              $absoluteFilePath = $file->getRealPath();
              $fileNameWithExtension = $file->getRelativePathname();
              $zip->addFile($absoluteFilePath, 'codigo/'.$fileNameWithExtension);
          }
        }
      }
      //descargo extras si es que hay
      if ($fs->exists("extras_ejercicios/".$id)) {
        $finder = new Finder();
        $finder->files()->in("extras_ejercicios/".$id);
        if(!empty($finder)){
          $zip -> addEmptyDir("extras");
          foreach ($finder as $file) {
              $absoluteFilePath = $file->getRealPath();
              $fileNameWithExtension = $file->getRelativePathname();
              $zip->addFile($absoluteFilePath, 'extras/'.$fileNameWithExtension);
          }
        }
      }

      $zip->close();

      //descargar Zip
      if(file_exists($zipName)){
        // Define headers
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=".$zipName);
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");
        // Read the file
        readfile($zipName);
        $fs->remove($zipName);

        exit;
      }else{
          echo 'The file does not exist.';
      }
      return $this->redirectToRoute('gamesReview_page');
      //return $this->render('prueba/prueba.html.twig');
    }

    #[Route('/descargarPlantilla', name: 'app_descargarPlantilla')]
    public function descargarPlantillaAction(): Response
    {
      $fileName = basename('index.html.twig');
      $filePath = 'plantilla/'.$fileName;
      if(!empty($fileName) && file_exists($filePath)){
          // Define headers
          header("Cache-Control: public");
          header("Content-Description: File Transfer");
          header("Content-Disposition: attachment; filename=$fileName");
          header("Content-Type: application/zip");
          header("Content-Transfer-Encoding: binary");

          // Read the file
          readfile($filePath);
          exit;
      }else{
          echo 'The file does not exist.';
      }
    }

    #[Route('/descargarEjemploCanvas', name: 'app_descargarEjemploCanvas')]
    public function descargarCanvasAction(): Response
    {
      $fileName = basename('ejemploCanvas.html.twig');
      $filePath = 'plantilla/'.$fileName;
      if(!empty($fileName) && file_exists($filePath)){
          // Define headers
          header("Cache-Control: public");
          header("Content-Description: File Transfer");
          header("Content-Disposition: attachment; filename=$fileName");
          header("Content-Type: application/zip");
          header("Content-Transfer-Encoding: binary");

          // Read the file
          readfile($filePath);
          exit;
      }else{
          echo 'The file does not exist.';
      }
    }

    #[Route('/descargarEjemploDiv', name: 'app_descargarEjemploDiv')]
    public function descargarDivAction(): Response
    {
      $fileName = basename('ejemploDiv.html.twig');
      $filePath = 'plantilla/'.$fileName;
      if(!empty($fileName) && file_exists($filePath)){
          // Define headers
          header("Cache-Control: public");
          header("Content-Description: File Transfer");
          header("Content-Disposition: attachment; filename=$fileName");
          header("Content-Type: application/zip");
          header("Content-Transfer-Encoding: binary");

          // Read the file
          readfile($filePath);
          exit;
      }else{
          echo 'The file does not exist.';
      }
    }
}
