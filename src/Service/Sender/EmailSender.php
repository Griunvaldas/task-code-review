<?php

namespace App\Service\Sender;

use App\Model\Message;

class EmailSender implements SenderInterface
{
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
        print "Email sent" . PHP_EOL;
    }
}
