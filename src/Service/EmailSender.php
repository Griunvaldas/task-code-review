<?php

namespace App\Service;

use App\Model\Message;

class EmailSender implements SenderInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(Message $message)
    {
        return $message->type == Message::TYPE_EMAIL;
    }

    /**
     * {@inheritDoc}
     *
     * @param Message $message
     */
    public function send(Message $message)
    {
        print "Email";
    }
}
