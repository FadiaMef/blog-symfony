<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++){
            $article = new Article();

            $article->setTitre("le titre de l'article $i")
                    ->setContenu("$i Lorem ipsum dolor sit amet, consectetur adipisicing elit. Delectus esse placeat iusto atque nesciunt eius accusamus unde error commodi quisquam minima provident rem odit sapiente, ducimus, at corrupti voluptatibus quidem adipisci. Dolorum aliquid tempora non in deleniti consequatur illo quisquam enim, maxime eius, eaque voluptates quia dignissimos amet voluptate nam!")
                    ->setDateDeCreation(new DateTime("now"));

            $manager->persist($article);
                }
        $manager->flush();
    }
}

