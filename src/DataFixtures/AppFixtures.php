<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($u=0; $u < mt_rand(5,10); $u++) {
            $user = new User();
            $hash = $this->passwordHasher->hashPassword($user,'password') ;
            $user->setEmail($faker->email)
                ->setLastName($faker->lastName)
                ->setFirstName($faker->firstName)
                ->setPassword($hash);
            $manager->persist($user);

            for ($c = 0; $c < mt_rand(3,20); $c++) {
                $customer = new Customer();
                $customer->setEmail($faker->email)
                    ->setCompany($faker->company)
                    ->setFirstName($faker->firstName)
                    ->setLastName($faker->lastName)
                    ->setUser($user);
                $manager->persist($customer);

                $chrono = 1;
                for ($i = 0; $i < mt_rand(1, 10); $i++) {
                    $invoice = new Invoice();
                    $invoice->setCustomer($customer)
                        ->setAmount($faker->randomFloat(2, 250, 5000))
                        ->setChrono($chrono)
                        ->setSentAt($faker->dateTimeBetween('-6 months'))
                        ->setStatus($faker->randomElement(['SENT', 'PAID', 'CANCELED']));
                    $chrono++;
                    $manager->persist($invoice);
                }
            }
        }


        $manager->flush();
    }
}
