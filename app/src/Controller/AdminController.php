<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_index")
     */
    public function index(MenuRepository $menuRepository): Response
    {
        $menuItems = $menuRepository->findBy([
            'deletedAt' => null
        ]);

        return $this->render('admin/index.html.twig', [
            'menuItems' => $menuItems
        ]);
    }

    /**
     * @Route("/admin/new-menu-item",  name="admin_new_menu_item")
     */
    public function new(Request $request): Response
    {
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $menu = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($menu);
            $em->flush();
            $this->addFlash('success', 'Menu item added successfully');
            return $this->redirectToRoute('admin_index');
        }
        $this->addFlash('danger', 'Menu item could not be added to database. Please try again.');
        return $this->renderForm('admin/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/admin/show-menu-item/{id}", name="admin_show_menu_item")
     */
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $menuItem = $doctrine->getRepository(Menu::class)->find($id);

        if (!$menuItem) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        return $this->render('admin/show.html.twig', [
            'menuItem' => $menuItem
        ]);
    }

    /**
     * @Route("/admin/edit-menu-item/{id}", name="admin_edit_menu_item")
     */
    public function edit(Request $request, Menu $menuItem): Response
    {
        $form = $this->createForm(MenuType::class, $menuItem);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($menuItem);
            $em->flush();
            $this->addFlash('success', 'Menu item modified');
            return $this->redirectToRoute('admin_index');
        }
        $this->addFlash('danger', 'Menu item could not be modified. Please try again.');
        return $this->renderForm('admin/edit.html.twig', [
            'form' => $form,
            'menuItem' => $menuItem
        ]);
    }

    /**
     * @Route("/admin/delete-menu-item/{id}", name="admin_delete_menu_item")
     */
    public function delete(Menu $menuItem)
    {
        try {
            $menuItem->setDeletedAt(new \DateTimeImmutable());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($menuItem);
            $entityManager->flush();
            $this->addFlash('success', 'Menu item deleted');
            return $this->redirectToRoute('admin_index');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Delete failed, try later');
            return $this->redirectToRoute('admin_index');
        }
    }
}