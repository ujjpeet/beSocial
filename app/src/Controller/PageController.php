<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
    * @Route("/", name="menu_page")
    */
    public function index(MenuRepository $menuRepository): Response
    {
        $menuItems = $menuRepository->findBy([
            'deletedAt' => null
        ]);

        return $this->render('menu.html.twig', [
            'menuItems' => $menuItems
        ]);
    }

    /**
     * @Route("/show-menu-item/{id}", name="show_menu_item")
     */
    public function show(MenuRepository $menuRepository, ManagerRegistry $doctrine, int $id): Response
    {
        $menuItem = $doctrine->getRepository(Menu::class)->find($id);
        $menuItems = $menuRepository->findBy([
            'deletedAt' => null
        ]);

        if (!$menuItem) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }
        return $this->render('show.html.twig', [
            'menuItem' => $menuItem,
            'menuItems' => $menuItems
        ]);
    }
}