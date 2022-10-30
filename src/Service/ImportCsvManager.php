<?php

namespace App\Service;

use App\Entity\Answer;
use App\Entity\Card;
use App\Entity\CardApocalypse;
use App\Entity\Question;
use App\Repository\CategoryRepository;
use App\Repository\FamilyRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;

class ImportCsvManager
{
    public function __construct(
        private QuestionRepository $questionRepository,
        private FamilyRepository $familyRepository,
        private CategoryRepository $categoryRepository,
        private EntityManagerInterface $manager
    ) {
    }

    public function importQuestionCsv(): void
    {
        $row = 0;
        $handle = fopen("assets/data/question.csv", "r");
        if ($handle !== false) {
            while (($data = fgetcsv($handle, null, ";")) !== false) {
                if ($row >  0) {
                    $question = new Question();
                    $question->setContent($data[0]);
                    $question->setLevel($data[1]);
                    $this->manager->persist($question);
                }
                $row++;
            }
            fclose($handle);
        }
        $this->manager->flush();
    }

    public function importAnswerCsv(): void
    {
        $row = 0;
        $handle = fopen("assets/data/question.csv", "r");
        if ($handle !== false) {
            while (($data = fgetcsv($handle, null, ";")) !== false) {
                if ($row > 0) {
                    $answer = new Answer();
                    $answer->setQuestion($this->questionRepository->findOneBy(['id' => $row]));
                    $answer->setContent($data[2]);
                    $answer->setIsCorrect($data[3]);
                    $this->manager->persist($answer);
                    for ($i = 0; $i < 3; $i++) {
                        $answer = new Answer();
                        $answer->setQuestion($this->questionRepository->findOneBy(['id' => $row]));
                        $answer->setContent('fausse réponse' . $row . '-' . $i);
                        $answer->setIsCorrect(false);
                        $this->manager->persist($answer);
                    }
                }
                $row++;
            }
            fclose($handle);
        }
        $this->manager->flush();
    }

    public function importCardCsv(): void
    {
        $row = 0;

        $handle = fopen("assets/data/cartes.csv", "r");
        if ($handle !== false) {
            while (($data = fgetcsv($handle, null, ";")) !== false) {
                if ($row >  0) {
                    $card = new Card();
                    $card->setFamily($this->familyRepository->findOneBy(['id' => intval($data[0] + 1)]));
                    $card->setName($data[1]);
                    $card->setDescription($data[2]);
                    $card->setImage($data[3]);
                    $card->setCredit(intval($data[4]));
                    $rule = ['association' => intval($data[5]), 'constraint' => $data[6],];
                    $card->setRule($rule);
                    $card->setCategory($this->categoryRepository->findOneBy(['id' => intval($data[7] + 1)]));
                    $this->manager->persist($card);
                }
                $row++;
            }
            fclose($handle);
        }
        $this->manager->flush();
    }

    public function importCardApocalypseCsv(): void
    {
        $row = 0;

        $handle = fopen("assets/data/apocalypse.csv", "r");
        if ($handle) {
            while (($data = fgetcsv($handle, null, ";"))) {
                if ($row >  0) {
                    $rule = [
                        'type' => $data[4], 'category' => $data[5], 'value' => intval($data[6]),
                        'exception' => intval($data[7])
                    ];
                    $apocalypse = new CardApocalypse();
                    $apocalypse->setFamily($this->familyRepository->findOneBy(['id' => intval($data[0] + 1)]));
                    $apocalypse->setName($data[1]);
                    $apocalypse->setDescription($data[2]);
                    $apocalypse->setImage($data[3]);
                    $apocalypse->setRule($rule);
                    $this->manager->persist($apocalypse);
                }
                $row++;
            }
            fclose($handle);
        }
        $this->manager->flush();
    }
}
