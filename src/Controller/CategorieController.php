<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\CategorieType;
use App\Form\CategorieModif;
use App\Form\CategorieDelete;
use App\Repository\CategorieRepository;
use App\Entity\Categorie;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

class CategorieController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategorieRepository $repo ): Response
    {
        $categories = $repo->findAll();

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categorie/ajouter', name: 'app_categorie_ajouter')]
    public function ajouter(Request $request, ManagerRegistry $doctrine): Response
    {
        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('categorie/ajout.html.twig',
    
            ['form' => $form->createView()]
        );
    }

    #[Route('/categorie/modifier{id}', name: 'app_categorie_modifier')]
    public function modifier($id, Request $request, ManagerRegistry $doctrine, CategorieRepository $repo): Response
    {
        $categorie = $repo->find($id);

        $forme = $this->createForm(CategorieModif::class, $categorie);

        $forme->handleRequest($request);
        if ($forme->isSubmitted() && $forme->isValid()) {

            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('categorie/modifier.html.twig',
            ['form' => $forme->createView()]
        );
    }

    #[Route('/categorie/supprimer{id}', name: 'app_categorie_supprimer')]
    public function supprimer($id, Request $request, ManagerRegistry $doctrine, CategorieRepository $repo): Response
    {
        $categorie = $repo->find($id);

        $formd = $this->createForm(CategorieDelete::class, $categorie);

        $formd->handleRequest($request);
        if ($formd->isSubmitted() && $formd->isValid()) {

            $em = $doctrine->getManager();
            $em->remove($categorie);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('categorie/supprimer.html.twig',
            ['form' => $formd->createView()]
        );
    }

    #[Route('/categorie/{id}', name: 'app_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }
    
}
