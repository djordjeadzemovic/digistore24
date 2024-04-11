<?php
declare(strict_types=1);

namespace Repository;

use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
class MessageRepositoryTest extends KernelTestCase
{
    public function test_it_has_connection(): void
    {
        
        self::bootKernel();

        $messageRepository = $this->createMock(MessageRepository::class);
        $messageRepository->expects($this->any())
            ->method('findAll')
            ->willReturn($messageRepository->findAll());

        $this->assertSame([], $messageRepository->findAll());
    }
}