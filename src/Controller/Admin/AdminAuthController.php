<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAuthController extends AbstractController
{
    /**
     * @Route(
     *     "/admin/login",
     *     methods={"GET", "POST"},
     *     name="admin_login"
     * )
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route(
     *     "/admin/logout",
     *     methods={"GET"},
     *     name="admin_logout"
     * )
     */
    public function logout(TokenStorage $tokenStorage): Response
    {
        $tokenStorage->setToken();

        return $this->redirectToRoute('admin_login');
    }
}
