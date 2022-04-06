<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Post extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $post = new \App\Entity\Post();
        $post->setTitle(uniqid());
        $manager->persist($post);

        $manager->flush();
    }
}
