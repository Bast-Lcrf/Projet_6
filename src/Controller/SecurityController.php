<?php

namespace App\Controller;

use App\Entity\Users;
use App\Service\SendMailService;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\SecurityFormType\RegistrationType;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SecurityFormType\ResetPasswordFormType;
use App\Form\SecurityFormType\ResetPasswordRequestType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    private SendMailService $sendMailService;
    private UsersRepository $usersRepository;

    public function __construct(SendMailService $sendMailService, UsersRepository $usersRepository)
    {
        $this->sendMailService = $sendMailService;
        $this->usersRepository = $usersRepository;
    }

    /**
     * This controller allow us to login
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/connexion', name: 'security.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * This controller allow us to logout
     *
     * @return void
     */
    #[Route('/deconnexion', name: 'security.logout')]
    public function logout()
    {
        // Nothing to do here..
    }

    /**
     * This controller allow us to register
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/inscription', name: 'security.registration', methods: ['GET', 'POST'])]
    public function registration(
        Request $request,
        EntityManagerInterface $manager,
        TokenGeneratorInterface $tokenGeneratorInterface
        ): Response
    {
        $user = new Users();
        $user->setRoles(['ROLE_USER']);
        $user->setToken($tokenGeneratorInterface->generateToken());
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->addFlash(
                'success',
                'Bienvenue ! Votre compte a bien été créé, un email de confirmation vous a été envoyé !'
            );

            $manager->persist($user);
            $manager->flush();
            $this->sendMailService->send(
                "no-replay@inscription-snowtricks.com",
                $user->getEmail(),
                'Validation d\'inscription',
                'emailValidationInscription',
                ["token" => $user->getToken()]
            );

            return $this->redirectToRoute('security.login');
        }

        return $this->render('security/registration.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }

    /**
     * This controller confirm the account with generated token
     *
     * @param  mixed $token
     * 
     * @return response|null
     */
    #[Route("/confirmer-mon-compte/{token}", name: "confirm.account")]
    public function confirmAccount(
        string $token,
        EntityManagerInterface $manager
        )
    {
        $user = $this->usersRepository->findOneBy(["token" => $token]);

        if($user) {
            $user->setToken(token: null);
            $user->setEnable(enable: true);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre Compte est vérifié et activé, Félicitation !'
            );
            return $this->redirectToRoute('security.login');
        } else {
            $this->addFlash(
                'error',
                'Le compte n\'existe pas !'
            );
            return $this->redirectToRoute('security.login');
        }
    }

    /**
     * this controller allow us to reset our password
     * Generate token
     * Generate url
     * Send mail
     *
     * @return void
     */
    #[Route('/mot-de-passe-oublie', name: 'security.forgotten.password')]
    public function forgottenPassword(
        Request $request,
        UsersRepository $usersRepository,
        TokenGeneratorInterface $tokenGeneratorInterface,
        EntityManagerInterface $manager,
        SendMailService $sendMailService
        ): Response
    {
        $form = $this->createForm(ResetPasswordRequestType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // On va chercher l'utilisateur par son email
            $user = $usersRepository->findOneByEmail($form->get('email')->getData());

            // On vérifie si on a un utilisateur
            if($user) {
                // On génére un token 
                $token = $tokenGeneratorInterface->generateToken();
                $user->setToken($token);
                $manager->persist($user);
                $manager->flush();

                // On génère un lien de réinitialisation du mot de passe
                $url = $this->generateUrl('security.reset.password', ["token" => $token], 
                UrlGeneratorInterface::ABSOLUTE_URL);
                
                // On créer les données du mail
                $context = compact('url', 'user');

                // Envoie du mail
                $sendMailService->send(
                    'no-reply@snowtricks.com',
                    $user->getEmail(),
                    'Réinitialisation du mot de passe',
                    'emailResetPassword',
                    $context
                );

                $this->addFlash(
                    'success',
                    'Email envoyé avec succés'
                );
                return $this->redirectToRoute('security.login');
            }
            // si $user est null
            $this->addFlash(
                'error',
                'Un probleme est survenu.'
            );
            return $this->redirectToRoute('security.login');
        }

        return $this->render('security/forgottenPassword.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }

    /**
     * This controller modify our password
     *
     * @return void
     */
    #[Route('/mot-de-passe-oublie/{token}', name: 'security.reset.password')] 
    public function resetPassword(
        string $token,
        Request $request,
        UsersRepository $usersRepository ,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
        ): Response
    {
        // On vérifie si on a ce token en base
        $user = $usersRepository->findOneByToken($token);
        
        if($user) {
            $form = $this->createForm(ResetPasswordFormType::class);

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                // On efface le token
                $user->setToken(token: null);
                $user->setPassword(
                    $hasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'Mot de passe modifié avec succés');
                return $this->redirectToRoute('security.login');
            }

            return $this->render('security/resetPassword.html.twig', [
                'resetPass' => $form->createView()
            ]);
        }
        $this->addFlash('error', 'Jeton invalide');
        return $this->redirectToRoute('security.login');
    }
}