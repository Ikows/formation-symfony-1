<?php

namespace App\Tests;

use App\Entity\Movie;
use App\Movie\AsyncMovieCreationCommand;
use App\Movie\MovieCreationCommand;
use App\Movie\RetrieveMoviesQuery;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\MessageBusInterface;

class MovieCreationTest extends KernelTestCase
{
    public function testAsyncCreationOfAMovie(): void
    {
        $kernel = self::bootKernel();

        $repo = static::getContainer()->get(MovieRepository::class);
        $em = static::getContainer()->get(EntityManagerInterface::class);
        foreach ($repo->findAll() as $item) {
            $em->remove($item);
        }
        $em->flush();


        $message = new AsyncMovieCreationCommand('Matrix', 'Description du film ');
        /** @var MessageBusInterface $messagesBus */
        $messagesBus = static::getContainer()->get(MessageBusInterface::class);
        // dispatch the message
        $messagesBus->dispatch($message);



        // run the messenger:consume command
        $consumeCommand = (new Application(self::$kernel))->find('messenger:consume');
        $commandTester = new CommandTester($consumeCommand);
        $commandTester->execute([
            'receivers' => ['async'],
            '--limit' => 1,
            '--time-limit' => 1
        ]);

        // assert database
        $movie = $repo->findByTitle('Matrix');
        static::assertCount(0, $movie);

        $consumeCommand = (new Application(self::$kernel))->find('messenger:failed:show');
        $commandTester = new CommandTester($consumeCommand);
        dump($commandTester->execute([]));

        $movie = $repo->findOneByTitle('Matrix');
        static::assertNotNull($movie);
        static::assertEquals('Matrix', $movie->getTitle());
        static::assertEquals('Description du film :', $movie->getDescription());

    }
}
