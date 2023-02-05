<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QuestionTest extends WebTestCase
{
    public function testIsNewIsSucessfull(): void
    {
        $client = static::createClient();

        // Récupérer et connecter un utilisateur avec le rôle Admin.
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@exemple.com');
        $client->loginUser($testUser);

        // Se rendre sur la page de création d'une question.
        $crawler = $client->request('GET', '/admin/question/new');

        // vérifier qu'il y a bien un h1 avec le text 'Créer une nouvelle question' sur cette page.
        $this->assertSelectorTextContains('h1', 'Créer une nouvelle question');

        // Gérer le formulaire.
        $buttonCrawlerNode = $crawler->selectButton('enregistrer');
        $form = $buttonCrawlerNode->form([
            'question[content]' => 'Ma nouvelle question de test',
            'question[level]'   => 3,
            'question[answers][0][content]' => 'Ma nouvelle réponse de test',
        ]);
//         $form['question[answers][0][isCorrect]']->tick();
        $client->submit($form);

        // Gérer la redirection.
        $this->assertResponseStatusCodeSame(Response::HTTP_SEE_OTHER);
        $client->followRedirect();

        // Vérifier le bon affichage du message flash.
        $this->assertSelectorTextContains('div.alert-success', 'La nouvelle question a bien été créée');

        $this->assertRouteSame('question_index');
    }
}
