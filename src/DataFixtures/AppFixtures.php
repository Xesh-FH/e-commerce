<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');


        for ($p = 0; $p < 100; $p++) {
            $product = new Product;
            $product
                ->setName($faker->sentence())
                ->setPrice(mt_rand(100, 5000))
                ->setSlug($faker->slug());
            $manager->persist($product);
        }
        $manager->flush();
    }
}
