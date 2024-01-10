<?php

declare(strict_types=1);

namespace App\Service\Sender;

use App\Model\Message;

class EmailSender implements SenderInterface
{
    public bool $isSent = false;

    public function supports(Message $message): bool
    {
        return $message->type == Message::TYPE_EMAIL;
    }

    /**
     * @param Message $message
     */
    public function send(Message $message): void
    {
        $this->isSent = true;
    }

    public function isSent(): bool
    {
        return $this->isSent;
    }
}
