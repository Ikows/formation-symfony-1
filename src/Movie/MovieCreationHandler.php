<?php

namespace App\Movie;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MovieCreationHandler implements MessageHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(MovieCreationCommand $movieCreationCommand)
    {
        $movie = new Movie();
        $movie
            ->setTitle($movieCreationCommand->title)
            ->setDescription($movieCreationCommand->description)
        ;

        $this->entityManager->persist($movie);
        $this->entityManager->flush();
    }
}