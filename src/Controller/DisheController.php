<?php

namespace App\Controller;

use App\Entity\Dishe;
use App\Form\DisheType;
use App\Repository\DisheRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dishe', name: 'dishe.')]
class DisheController extends AbstractController
{
    #[Route('/', name: 'edit')]
    public function index(DisheRepository $ds): Response
    {
        $dishes = $ds->findAll();

        return $this->render('dishe/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(ManagerRegistry $doctrine, Request $request)
    {
        $dishe = new Dishe();

        // Form
        $form = $this->createForm(DisheType::class, $dishe);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            // Entity manager
            $em = $doctrine->getManager();

            $image = $form->get('Attachment')->getdata();

            if($image){
                $dataName = md5(uniqid()). '.' . $image->guessClientExtension();
            }

            $image->move(
                $this->getParameter('images_folder'),
                $dataName
            );

            $dishe->setImage($dataName);

            $em->persist($dishe);
            $em->flush();

            return $this->redirect($this->generateUrl('dishe.edit'));
        }

        // Response
        return $this->render('dishe/create.html.twig', [
            'createForm' => $form->createView() 
        ]);
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove($id, DisheRepository $ds, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $dishe = $ds->find($id);
        $em->remove($dishe);
        $em->flush();

        // message
        $this->addFlash('success', 'Dish was removed successfully');

        return $this->redirect($this->generateUrl('dishe.edit'));
    }

    #[Route('/show/{id}', name: 'show')]
    public function show(Dishe $dishe)
    {
        return $this->render('dishe/show.html.twig', [
            'dishe' => $dishe 
        ]);
    }

}
