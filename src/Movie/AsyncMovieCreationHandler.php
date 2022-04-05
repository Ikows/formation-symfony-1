<?php

namespace App\Movie;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AsyncMovieCreationHandler implements MessageHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function __invoke(AsyncMovieCreationCommand $movieCreationCommand)
    {
        $movie = new Movie();
        $movie
            ->setTitle($movieCreationCommand->title)
            ->setDescription($movieCreationCommand->description)
        ;

        $violations = $this->validator->validate($movie);
        if ($violations->count() > 0) {
            throw new InvalidMovieException();
        }
        $this->entityManager->persist($movie);
        $this->entityManager->flush();
    }

    public static function getHandledMessages(): iterable
    {
        yield MovieCreationCommand::class;
        yield AsyncMovieCreationCommand::class;
    }
}