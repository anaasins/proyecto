<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Usuario;
use App\Repository\UsuarioRepository;

class UsuarioController extends AbstractController
{
    #[Route('/profile', name: 'profile_page')]
     public function profileAction(): Response
     {
         return $this->render('usuario/index.html.twig');
     }

    #[Route('/datos', name: 'datos_page')]
    public function datosAction(UsuarioRepository $usuarioRepository): Response
    {
        $datos = $usuarioRepository->findById(1);
        return $this->render('usuario/datos.html.twig', array('datos'=>$datos));
    }

}
