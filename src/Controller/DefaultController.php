<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Ejercicio;
use App\Repository\EjercicioRepository;

class DefaultController extends AbstractController
{
  /**
   * @Route("/", name="home_page")
   */
    public function homeAction(EjercicioRepository $ejercicioRepository): Response
    {
      //Capturar el repositorio de la tabla contra la bd
      $ejercicios = $ejercicioRepository->findThreeToShow();
      return $this->render('frontal/index.html.twig', array('ejercicios'=>$ejercicios));
    }
    /**
    * @Route("/login", name="login_page")
     * login_page
     */
    public function loginAction(): Response
    {
        return $this->render('frontal/login.html.twig');
    }

    /**
    * @Route("/contact", name="contact_page")
     * contact_page
     */
    public function contactAction(): Response
    {
        return $this->render('frontal/contact.html.twig');
    }

    /**
    * @Route("/profile", name="profile_page")
     * games_page
     */
    public function profileAction(): Response
    {
        return $this->render('frontal/perfil.html.twig');
    }

}

 ?>
