<?php

namespace App\Controller;

use App\Entity\Chaton;
use App\Form\ChatonType;
use App\Repository\ChatonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chaton')]
final class ChatonController extends AbstractController
{
    #[Route(name: 'app_chaton_index', methods: ['GET'])]
    public function index(ChatonRepository $chatonRepository): Response
    {
        return $this->render('chaton/index.html.twig', [
            'chatons' => $chatonRepository->findAll(),
        ]);
    }

    # page ajout
    #[Route('/new', name: 'app_chaton_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chaton = new Chaton();
        $form = $this->createForm(ChatonType::class, $chaton);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('Photo')->getData();
            if (file_exists($photo)) {
                $photoName = uniqid().'.'.$photo->guessExtension();
                if (!file_exists("images")) {
                    mkdir("images");
                }
                $photo->move("images", $photoName);
                $chaton->setPhoto($photoName);
            }

            $entityManager->persist($chaton);
            $entityManager->flush();

            return $this->redirectToRoute('app_chaton_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chaton/new.html.twig', [
            'chaton' => $chaton,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chaton_show', methods: ['GET'])]
    public function show(Chaton $chaton): Response
    {
        return $this->render('chaton/show.html.twig', [
            'chaton' => $chaton,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chaton_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chaton $chaton, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChatonType::class, $chaton);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_chaton_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chaton/edit.html.twig', [
            'chaton' => $chaton,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chaton_delete', methods: ['POST'])]
    public function delete(Request $request, Chaton $chaton, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chaton->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($chaton);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chaton_index', [], Response::HTTP_SEE_OTHER);
    }
}
