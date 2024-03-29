<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistryController extends AbstractController
{
    #[Route('/reg', name: 'reg')]
    public function reg(Request $request, UserPasswordHasherInterface $passEncoder, ManagerRegistry $doctrine): Response
    {

        $regForm= $this->createFormBuilder()
        ->add('username', TextType::class,[
            'label' => 'Employee'
        ])
        ->add('password', RepeatedType::class,[
            'type' => PasswordType::class,
            'required' => true,
            'first_options' => ['label' => 'Password'],
            'second_options' => ['label' => 'Repeated Password']
        ])
        ->add('Register', SubmitType::class)
        ->getForm();

        $regForm->handleRequest($request);

        if($regForm->isSubmitted()){
            $input = $regForm->getData();

            $user = new User();
            $user->setUsername($input['username']);

            $user->setPassword(
                $passEncoder->hashPassword($user, $input['password'])
            );
            
            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('registry/index.html.twig', [
            'regForm' => $regForm->createView()
        ]);
    }
}
