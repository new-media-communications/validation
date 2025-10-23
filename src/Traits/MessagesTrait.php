<?php

namespace Nmc\Validation\Traits;

trait MessagesTrait
{
    /** @var array<string, string> */
    protected $messages = [];

    /**
     * Given $key and $message to set message
     */
    public function setMessage(string $key, string $message): void
    {
        $this->messages[$key] = $message;
    }

    /**
     * Given $messages and set multiple messages
     *
     * @param  array<string, string>  $messages
     */
    public function setMessages(array $messages): void
    {
        $this->messages = array_merge($this->messages, $messages);
    }

    /**
     * Given message from given $key
     */
    public function getMessage(string $key): string
    {
        return array_key_exists($key, $this->messages) ? $this->messages[$key] : $key;
    }

    /**
     * Get all $messages
     *
     * @return array<string, string>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
