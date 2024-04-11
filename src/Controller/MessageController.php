<?php
declare(strict_types=1);

namespace App\Controller;

use App\Message\SendMessage;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for managing messages.
 *
 *  */
class MessageController extends AbstractController
{
    public MessageRepository $messageRepository;
    private MessageBusInterface $messageBus;

    /**
     * Moving dependancy here in constructor, it makes our life easer when it comes to testing.
     */
    public function __construct(MessageRepository $messageRepository, MessageBusInterface $messageBus)
    {
        $this->messageRepository = $messageRepository;
        $this->messageBus = $messageBus;
    }

    /**
     * Retrieves a list of messages.
     *  @param Request $request
     * @return JsonResponse
     * **/
    // Use JsonResponse instead of Response, it is more convinient and we do not need to use json_encode
    #[Route('/messages', methods: ['GET'])]

    public function list(Request $request): JsonResponse
    {
        $messages = $this->messageRepository->fetchData($request);

        $messagesOutput = [];

        foreach ($messages as $message) {
            $messagesOutput[] = [
                'uuid' => $message->getUuid(),
                'text' => $message->getText(),
                'status' => $message->getStatus(),
            ];
        }
        
        return new JsonResponse(['messages' => $messagesOutput]);
    }
    /**
     * Sends a message.
     * @param Request $request
     * @return JsonResponse
     * **/
    // Use JsonResponse instead of Response, it is more convinient and we do not need to use json_encode
    // Also, I think method should be POST and route should be renamed (cannot be tested via browser but can with Postman)
    #[Route('/message/send', methods: ['GET'])]

    public function send(Request $request): JsonResponse
    {
        $text = $request->query->get('text');

        if (empty($text)) {
            return new JsonResponse(['error' => 'Text is required'], Response::HTTP_BAD_REQUEST);
        }

        $this->messageBus->dispatch(new SendMessage((String) $text));

        return new JsonResponse(['message' => 'Successfully sent'], Response::HTTP_NO_CONTENT);
    }
}