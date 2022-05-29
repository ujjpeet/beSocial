<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\File;

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
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $menuItem = new Menu();
        $form = $this->createForm(MenuType::class, $menuItem);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //az egyszerűség kedvéért a fájlkezelés nincs kiszervezve service-be
            $imageFile = $form->get('imageUrl')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Menu item is not saved, because image could not be saved on server. '.$e);
                    return $this->redirectToRoute('admin_index');
                }
            }

            try{
                $menuItem = $form->getData();
                $menuItem->setImageUrl($newFilename);
                $em = $this->getDoctrine()->getManager();
                $em->persist($menuItem);
                $em->flush();
                $this->addFlash('success', 'Menu item has been added successfully');
                return $this->redirectToRoute('admin_index');
            }  catch (\Exception $e) {
                    $this->addFlash('danger', 'Menu item could not be added to database. Please try again.'. $e);
                    return $this->redirectToRoute('admin_index');
            }
        }
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
    public function edit(Request $request, Menu $menuItem, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(MenuType::class, $menuItem);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('imageUrl')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Menu item is not updated, because image could not be saved on server. '. $e);
                    return $this->redirectToRoute('admin_index');
                }
            }

            try {
                $menuItem->setImageUrl($newFilename);
                $em = $this->getDoctrine()->getManager();
                $em->persist($menuItem);
                $em->flush();
                $this->addFlash('success', 'Menu item modified');
                return $this->redirectToRoute('admin_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Menu item could not be modified. Please try again.' . $e);
                return $this->redirectToRoute('admin_index');
            }
        }
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
            $this->addFlash('success', 'Menu item is deleted');
            return $this->redirectToRoute('admin_index');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Delete failed, try later. ' . $e);
            return $this->redirectToRoute('admin_index');
        }
    }
}