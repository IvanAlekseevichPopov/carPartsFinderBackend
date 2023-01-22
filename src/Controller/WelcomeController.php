<?php

namespace App\Controller;

use App\Admin\SonataController;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class WelcomeController extends AbstractController
{
    #[Route('/', name: 'welcome')]
    public function index(UserRepository $repository, RouterInterface $router): Response
    {
        foreach ($router->getRouteCollection() as $item) {
            dump($item->getPath());
        }

        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'WelcomeController',
        ]);
    }
}
