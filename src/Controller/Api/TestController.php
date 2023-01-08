<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/api/test', name: 'app_test')]
    public function index(): array
    {
        return [
            'message' => 'Welcome to your new controller!',
        ];
    }
}
