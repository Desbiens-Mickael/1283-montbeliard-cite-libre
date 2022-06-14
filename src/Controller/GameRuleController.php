<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'game_rule_')]
class GameRuleController extends AbstractController
{
    #[Route('game/rule', name: 'index')]
    public function index(): Response
    {
        return $this->render('game_rule/index.html.twig', [
            'controller_name' => 'GameRuleController',
        ]);
    }
}