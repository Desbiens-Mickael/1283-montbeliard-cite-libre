<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Classe;
use App\Entity\Family;
use Doctrine\ORM\EntityManagerInterface;

class StartupDataManager
{
    private const CATEGORIES = [
        "artisan",
        "marchand",
    ];
    private const CLASSES = [
        '6A', '6B', '6C',
        '5A', '5B', '5C',
        '4A', '4B', '4C',
        '3A', '3B', '3C',
    ];
    private const FAMILIES = [
        "Métal",
        "Métiers du bois et
        de la poterie... et du sel",
        "La pierre et le verre",
        "La construction de l'habitat",
        "La construction des palais et cathédrales",
        "Les fabricants d'outils et d'accessoires",
        "Le travail des alliages (les petits objets)",
        "Travail des alliages (les gros objets)",
        "Les métiers autour de la farine",
        "Les métiers de bouche",
        "Les métiers du cuir et du textile",
        "Les métiers intellectuels et artistiques",
        "Les marchands",
        "Cavaliers de l'apocalypse",
    ];

    public function __construct(
        private EntityManagerInterface $manager,
        private ImportCsvManager $importCsvManager
    ) {
    }

    public function generateDataFromArray(): void
    {
        foreach (self::CATEGORIES as $value) {
            $category = new Category();
            $category->setName($value);
            $this->manager->persist($category);
        }

        foreach (self::CLASSES as $classe) {
            $classeStudent = new Classe();
            $classeStudent->setClasse($classe);
            $this->manager->persist($classeStudent);
        }

        foreach (self::FAMILIES as $value) {
            $family = new Family();
            $family->setName($value);
            $this->manager->persist($family);
        }

        $this->manager->flush();
    }

    public function generatingDataFromCsvFile(): void
    {
        $this->importCsvManager->importQuestionCsv();
        $this->importCsvManager->importAnswerCsv();
        $this->importCsvManager->importCardCsv();
        $this->importCsvManager->importCardApocalypseCsv();
    }
}
