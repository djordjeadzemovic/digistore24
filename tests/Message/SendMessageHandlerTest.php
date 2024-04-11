<?php

declare(strict_types=1);

namespace Message;

use App\Entity\Message;
use App\Message\SendMessage;
use App\Message\SendMessageHandler;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\Tools\SchemaTool;

class SendMessageHandlerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    // protected function setUp(): void
    // {
    //     // Set up the EntityManagerInterface
    //     // Mocking the EntityManagerInterface could also be done using a mocking framework like PHPUnit's MockObject
    //     $this->entityManager = $this->createMock(EntityManagerInterface::class);

    //     // Create the database schema
    //     $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
    //     $schemaTool = new SchemaTool($this->entityManager);
    //     $schemaTool->dropDatabase();
    //     $schemaTool->createSchema($metadata);
    // }

    public function testInvoke(): void
    {
        /**
         * Since this function with void return type, we cannot cpmapre return value,
         * but since this finction persist data into database,
         * we can determine if there is a new row in table.
         */
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        // Create a SendMessage object
        $sendMessage = new SendMessage('Test message');

        // Create a SendMessageHandler object
        $handler = new SendMessageHandler($this->entityManager);

        // Invoke the handler
        $handler($sendMessage);

        // Query the database to check if the new record is inserted
        $messageRepository = $this->entityManager->getRepository(Message::class);
        $messages = $messageRepository->findAll();

        // Assert that a new record is inserted
        $this->assertCount(1, $messages);
        $this->assertSame('Test message', $messages[0]->getText());
        $this->assertSame('sent', $messages[0]->getStatus());
    }
}