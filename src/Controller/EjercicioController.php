<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\EjercicioType;
use App\Entity\Ejercicio;
use App\Repository\EjercicioRepository;

class EjercicioController extends AbstractController
{
    #[Route('/games', name: 'games_page')]
    public function gamesAction(EjercicioRepository $ejercicioRepository): Response
    {
        $ejercicios = $ejercicioRepository->findAllToShow();
        return $this->render('ejercicio/index.html.twig', array('ejercicios'=>$ejercicios));
    }

    #[Route('/newGame', name: 'newGame_page')]
    public function newGameAction(Request $request): Response
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
        if(!is_null($request)){
          $datos = $request->request->all();
          $file = $form['imagen']->getData();
          $file->move("img/ejercicios/", "nueva.png");
        }
        return $this->renderForm('ejercicio/anyadir.html.twig', ['form'=>$form,]);
    }
}
