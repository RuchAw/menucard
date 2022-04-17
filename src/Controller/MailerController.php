<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/mail', name: 'mail')]
    public function sendEmail(MailerInterface $mailer, Request $request): Response
    {        
        $emailForm = $this->createFormBuilder()
                ->add('message', TextareaType::class, [
                    'attr' => array('rows' => '5')
                ])
                ->add('send', SubmitType::class,[
                    'attr' => [
                        'class' => 'btn btn-outline-danger float-right'
                    ]
                ])
                ->getForm();

        $emailForm->handleRequest($request);        
        
        if ($emailForm->isSubmitted()){

            $input = $emailForm->getData();
            $text = $input['message'];
            $tab = 'table1';


            $email = (new TemplatedEmail())
                    ->from('table1@menucard.wip')
                    ->to('waiter@menucard.wip')
                    ->subject('Order')
                    ->htmlTemplate('mailer/mail.html.twig')
                    ->context([
                        'tab' => $tab,
                        'text' => $text
                    ]);

            $mailer->send($email);
            $this->addFlash('message', 'Message was sent');

            return $this->redirect($this->generateUrl('mail'));
        }

        return $this->render('mailer/index.html.twig',[
            'emailForm' => $emailForm->createView()
        ]);

    }
}