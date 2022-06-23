<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\User;
use Bezhanov\Faker\Provider\Commerce;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Liior\Faker\Prices;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;

    public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');
        $faker->addProvider(new Prices($faker));
        $faker->addProvider(new Commerce($faker));
        $faker->addProvider(new PicsumPhotosProvider($faker));

        $admin = new User;
        $hash = $this->encoder->encodePassword($admin, "password");
        $admin
            ->setEmail("admin@gmail.com")
            ->setPassword($hash)
            ->setFullName("Admin")
            ->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);


        $users = [];
        for ($u = 0; $u < 5; $u++) {
            $user = new User;
            $hash = $this->encoder->encodePassword($user, "password");
            $user
                ->setEmail("user$u@gmail.com")
                ->setFullName($faker->name())
                ->setPassword($hash);
            $users[] = $user;
            $manager->persist($user);
        }

        $products = [];

        for ($c = 0; $c < 3; $c++) {
            $category = new Category;
            $category
                ->setName($faker->department)
                ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            for ($p = 0; $p < mt_rand(15, 20); $p++) {
                $product = new Product;
                $product
                    ->setName($faker->productName)
                    ->setPrice($faker->price(4000, 20000))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture($faker->imageUrl(400, 400, true));

                $products[] = $product;
                $manager->persist($product);
            }

            for ($p = 0; $p < mt_rand(20, 40); $p++) {
                $purchase = new Purchase;

                $purchase
                    ->setFullName($faker->name)
                    ->setAdress($faker->streetAddress)
                    ->setPostalCode($faker->postcode)
                    ->setCity($faker->city)
                    //On associe la commande à un utilisateur aléatoirement.
                    ->setUser($faker->randomElement($users))
                    ->setTotal(mt_rand(2000, 30000))
                    ->setPurchasedAt($faker->dateTimeBetween('-6 months'));

                $selectedProducts = $faker->randomElements($products, mt_rand(2, 6));

                foreach ($selectedProducts as $product) {
                    $purchaseItem = new PurchaseItem;
                    $purchaseItem
                        ->setProduct($product)
                        ->setQuantity(mt_rand(1, 3))
                        ->setProductName($product->getName())
                        ->setProductPrice($product->getPrice())
                        ->setTotal(
                            $purchaseItem->getProductPrice() * $purchaseItem->getQuantity()
                        )
                        ->setPurchase($purchase);
                    $manager->persist($purchaseItem);
                }

                // booléen aléatoire avec 90% de chances de true.
                if ($faker->boolean(90)) {
                    $purchase->setStatus(Purchase::STATUS_PAYED);
                }
                $manager->persist($purchase);
            }

            $manager->flush();
        }
    }
}
