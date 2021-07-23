<?php

namespace App\Service;


use App\Model\Message;

class Messenger
{
    /**
     * @var SenderInterface[]
     */
    protected $senders = [];

    /**
     * Messenger constructor.
     * @param SenderInterface[] $senders
     */
    public function __construct(array $senders = [])
    {
        $this->senders = $senders;
    }

    /**
     * {@inheritDoc}
     *
     */
    public function send(Message $message)
    {
        foreach ($this->senders as $sender) {
            if ($sender->supports($message)) {
                return $sender->send($message);
            }
        }
    }
}
