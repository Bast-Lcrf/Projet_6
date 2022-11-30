<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer extends AbstractController
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    
    public function sendmail(string $email,string $token): Response
    {
        $email = (new TemplatedEmail())
        ->from('inscription@snowtrick.com')
        ->to(new Address($email))
        ->subject('Validation d\'inscription')
    
        // path of the Twig template to render
        ->htmlTemplate('email/emailValidationInscription.html.twig')
    
        // pass variables (name => value) to the template
        ->context([
            'token' => $token
        ]);

        $this->mailer->send($email);

        return $this->redirectToRoute("security.login");
    }
}