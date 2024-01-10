<?php

declare(strict_types=1);

namespace App\Service;


use App\Model\Message;
use App\Service\Sender\SenderInterface;

class Messenger
{
    /**
     * @var null|SenderInterface
     */
    protected ?SenderInterface $sender = null;

    /**
     * Messenger constructor.
     * @param SenderInterface $sender
     */
    public function __construct(SenderInterface $sender)
    {
        $this->sender = $sender;
    }

    public function send(Message $message): void
    {
        if ($this->sender === null || !$this->sender->supports($message)) {
            return;
        }

        $this->sender->send($message);
    }

    public function isSent(): bool
    {
        return $this->sender->isSent();
    }
}
