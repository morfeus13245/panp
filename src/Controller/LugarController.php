<?php

namespace App\Controller;

use App\Entity\Lugar;
use App\Form\LugarType;
use App\Repository\LugarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lugar")
 */
class LugarController extends AbstractController
{
    /**
     * @Route("/", name="lugar_index", methods={"GET"})
     */
    public function index(LugarRepository $lugarRepository): Response
    {
        return $this->render('lugar/index.html.twig', [
            'lugars' => $lugarRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="lugar_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lugar = new Lugar();
        $form = $this->createForm(LugarType::class, $lugar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lugar);
            $entityManager->flush();

            return $this->redirectToRoute('lugar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lugar/new.html.twig', [
            'lugar' => $lugar,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lugar_show", methods={"GET"})
     */
    public function show(Lugar $lugar): Response
    {
        return $this->render('lugar/show.html.twig', [
            'lugar' => $lugar,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lugar_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Lugar $lugar, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LugarType::class, $lugar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('lugar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lugar/edit.html.twig', [
            'lugar' => $lugar,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lugar_delete", methods={"POST"})
     */
    public function delete(Request $request, Lugar $lugar, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lugar->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lugar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('lugar_index', [], Response::HTTP_SEE_OTHER);
    }
}
