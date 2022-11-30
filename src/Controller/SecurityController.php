<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Services\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private Mailer $mailer;
    private UserRepository $userRepository;

    public function __construct(Mailer $mailer, UserRepository $userRepository)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
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
    public function registration(Request $request, EntityManagerInterface $manager, Mailer $mailer): Response
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $user->setToken($this->generateToken());
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
            $this->mailer->sendmail($user->getEmail(), $user->getToken());

            return $this->redirectToRoute('security.login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller confirm the account with generated token
     *
     * @param  mixed $token
     * @return void
     */
    #[Route("/confirmer-mon-compte/{token}", name: "confirm.account")]
    public function confirmAccount(string $token, EntityManagerInterface $manager)
    {
        $user = $this->userRepository->findOneBy(["token" => $token]);

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
     * Generate random token,
     * Used For account validation
     * Used for password loss verification
     *
     * @return string
     * @throws Exception
     */
    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(length: 32)), '+/', '-_'));
    }
}
