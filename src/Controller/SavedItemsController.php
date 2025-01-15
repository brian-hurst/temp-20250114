<?php

namespace App\Controller;

use App\Entity\SavedItems;
use App\Form\SavedItemsType;
use App\Repository\SavedItemsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/saved/items')]
final class SavedItemsController extends AbstractController{
    #[Route(name: 'app_saved_items_index', methods: ['GET'])]
    public function index(SavedItemsRepository $savedItemsRepository): Response
    {
        return $this->render('saved_items/index.html.twig', [
            'saved_items' => $savedItemsRepository->findAll(),
        ]);
    }

    #[Route('/save', name: 'app_saved_items_save', methods: ['POST'])]
    public function save(Request $request, EntityManagerInterface $entityManager): Response
    {
        $savedItem = new SavedItems();
        $form = $this->createForm(SavedItemsType::class, $savedItem);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$this->isCsrfTokenValid('saved_items_form', $request->request->get('_token'))) {
                return $this->json(['error' => 'Invalid CSRF token'], 400);
            }
            $entityManager->persist($savedItem);
            $entityManager->flush();

            return $this->json(['message' => 'Successfully saved.'], 201);
        }
        return $this->json(['error' => 'Invalid form'], 400);
    }

    #[Route('/new', name: 'app_saved_items_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $savedItem = new SavedItems();
        $form = $this->createForm(SavedItemsType::class, $savedItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($savedItem);
            $entityManager->flush();

            return $this->redirectToRoute('app_saved_items_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('saved_items/new.html.twig', [
            'saved_item' => $savedItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_saved_items_show', methods: ['GET'])]
    public function show(SavedItems $savedItem): Response
    {
        return $this->render('saved_items/show.html.twig', [
            'saved_item' => $savedItem,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_saved_items_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SavedItems $savedItem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SavedItemsType::class, $savedItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_saved_items_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('saved_items/edit.html.twig', [
            'saved_item' => $savedItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_saved_items_delete', methods: ['POST'])]
    public function delete(Request $request, SavedItems $savedItem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$savedItem->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($savedItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_saved_items_index', [], Response::HTTP_SEE_OTHER);
    }
}
