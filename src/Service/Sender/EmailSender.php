<?php

namespace App\Service\Sender;

use App\Model\Message;

class EmailSender implements SenderInterface
{
    public bool $isSent = false;

    /**
     * {@inheritDoc}
     */
    public function supports(Message $message): bool
    {
        return $message->type == Message::TYPE_EMAIL;
    }

    /**
     * {@inheritDoc}
     *
     * @param Message $message
     */
    public function send(Message $message): void
    {
        $this->isSent = true;
    }

    /**
     * {@inheritDoc}
     */
    public function isSent(): bool
    {
        return $this->isSent;
    }
}
