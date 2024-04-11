<?php
declare(strict_types=1);

namespace Controller;

use App\Message\SendMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;
use App\Repository\MessageRepository;
use App\Entity\Message;
use App\Controller\MessageController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;

class MessageControllerTest extends WebTestCase
{
    use InteractsWithMessenger;

    public function testList(): void
    {
        // Mock MessageRepository
        $messageRepository = $this->createMock(MessageRepository::class);
        // Mock MessageBusInterface 
        $messageBusInterface = $this->createMock(MessageBusInterface::class);

        $message1 = new Message();
        $message1->setUuid("1eef784e-3845-66d4-9f73-e9d4a65aa989");
        $message1->setText("Sunt doloribus dolorum explicabo possimus ipsum harum quasi cum.");
        $message1->setStatus("read");

        $fakeMessages = [
            $message1,
        ];
        
        $messageRepository->expects($this->once())
            ->method('fetchData')
            ->willReturn($fakeMessages);

        // Create MessageController object
        $messageController = new MessageController($messageRepository, $messageBusInterface);

        // Create a mock Request object
        $request = $this->createMock(Request::class);

        // Call the list function
        $response = $messageController->list($request);

        // Assertions
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $responseData = json_decode(is_string ($response->getContent()) ? $response->getContent() : '', true);
        $arrayResponseData = is_array($responseData) ? $responseData : [];
        $this->assertArrayHasKey('messages', (array) $arrayResponseData);
        $fakeMessagesArray = is_array($fakeMessages) ? $fakeMessages : [];
        $this->assertCount(count($fakeMessagesArray), $arrayResponseData['messages']);
        // Assert each message
        foreach ($fakeMessages as $index => $fakeMessage) {
            $this->assertSame($fakeMessage->getUuid(), $arrayResponseData['messages'][$index]['uuid']);
            $this->assertSame($fakeMessage->getText(), $arrayResponseData['messages'][$index]['text']);
            $this->assertSame($fakeMessage->getStatus(), $arrayResponseData['messages'][$index]['status']);
        }
    }

    function test_that_it_sends_a_message(): void
    {
        $client = static::createClient();
        $client->request('GET', 'message/send', [
            'text' => 'Hello World',
        ]);

        $this->assertResponseIsSuccessful();
        // This is using https://packagist.org/packages/zenstruck/messenger-test
        $this->transport('sync')
            ->queue()
            ->assertContains(SendMessage::class, 1);
    }
}