<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\MicroPost;
use DateTime;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $microPost = new MicroPost();
        $microPost->setTitle('Free Palestine');
        $microPost->setText('Fuck Israel!');
        $microPost->setCreated(new DateTime());

        $manager->persist($microPost);

        $manager->flush();
    }
}
