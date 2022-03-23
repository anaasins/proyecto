<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use App\Repository\RolRepository;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class UsuarioController extends AbstractController
{

    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/profile', name: 'profile_page')]
     public function profileAction(): Response
     {
       $this->denyAccessUnlessGranted('ROLE_USER');

         return $this->render('usuario/index.html.twig');
     }

    #[Route('/datos', name: 'datos_page')]
    public function datosAction(UsuarioRepository $usuarioRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        //$datos = $usuarioRepository->findById(1);
        return $this->render('usuario/datos.html.twig', array('usuario'=>$user));
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, RolRepository $rolRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Usuario();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setFechaRegistro(new \DateTime('@'.strtotime('now')));
            $rol = $rolRepository->findById(2);
            $user->setRol($rol[0]);
            $user->setIsVerified(true);

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
          /*  $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('aasins97@gmail.com', 'Ana'))
                    ->to($user->getCorreo())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );*/
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('usuario/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }

    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('usuario/login.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
      throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    //Activar en security.yaml si realment es necessari guardar el utim acces per algo
    #[Route('/update', name: 'app_update')]
    public function update(EntityManagerInterface $entityManager): Response
    {
      $user= $this->getUser();
      $user->setUltimoAcceso(new \DateTime('@'.strtotime('now')));
      $entityManager->persist($user);
      $entityManager->flush();
      return $this->redirectToRoute('home_page');
    }
}
