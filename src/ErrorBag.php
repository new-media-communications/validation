<?php

namespace Rakit\Validation;

class ErrorBag
{
    /** @var array<string, array<string, string>> */
    protected $messages = [];

    /**
     * @param  array<string, array<string, string>>  $messages
     */
    public function __construct(array $messages = [])
    {
        $this->messages = $messages;
    }

    /**
     * Add message for given key and rule
     */
    public function add(string $key, string $rule, string $message): void
    {
        if (! isset($this->messages[$key])) {
            $this->messages[$key] = [];
        }

        $this->messages[$key][$rule] = $message;
    }

    /**
     * Get messages count
     */
    public function count(): int
    {
        return count($this->all());
    }

    /**
     * Check given key is existed
     */
    public function has(string $key): bool
    {
        [$key, $ruleName] = $this->parsekey($key);
        if ($this->isWildcardKey($key)) {
            $messages = $this->filterMessagesForWildcardKey($key, $ruleName);

            return Helper::arrayDot($messages) !== [];
        } else {
            $messages = $this->messages[$key] ?? null;

            if (! $ruleName) {
                return ! empty($messages);
            } else {
                return ! empty($messages) && isset($messages[$ruleName]);
            }
        }
    }

    /**
     * Get the first value of array
     */
    public function first(string $key): ?string
    {
        [$key, $ruleName] = $this->parsekey($key);
        if ($this->isWildcardKey($key)) {
            $messages = $this->filterMessagesForWildcardKey($key, $ruleName);

            /**
             * @var array<string, string>
             */
            $flattenMessages = Helper::arrayDot($messages);

            return array_shift($flattenMessages);
        } else {
            $keyMessages = $this->messages[$key] ?? [];

            if (empty($keyMessages)) {
                return null;
            }

            if ($ruleName) {
                return $keyMessages[$ruleName] ?? null;
            } else {
                return array_shift($keyMessages);
            }
        }
    }

    /**
     * Get messages from given key, can be use custom format
     *
     * @return array<string, string|array<string, string>>
     */
    public function get(string $key, string $format = ':message'): array
    {
        [$key, $ruleName] = $this->parsekey($key);
        $results = [];
        if ($this->isWildcardKey($key)) {
            $messages = $this->filterMessagesForWildcardKey($key, $ruleName);
            foreach ($messages as $explicitKey => $keyMessages) {
                foreach ($keyMessages as $rule => $message) {
                    $results[$explicitKey][$rule] = $this->formatMessage($message, $format);
                }
            }
        } else {
            $keyMessages = $this->messages[$key] ?? [];
            foreach ($keyMessages as $rule => $message) {
                if ($ruleName && $ruleName != $rule) {
                    continue;
                }

                $results[$rule] = $this->formatMessage($message, $format);
            }
        }

        return $results;
    }

    /**
     * Get all messages
     *
     * @return string[]
     */
    public function all(string $format = ':message'): array
    {
        $messages = $this->messages;
        $results = [];
        foreach ($messages as $keyMessages) {
            foreach ($keyMessages as $message) {
                $results[] = $this->formatMessage($message, $format);
            }
        }

        return $results;
    }

    /**
     * Get the first message from existing keys
     *
     * @return array<string, string>
     */
    public function firstOfAll(string $format = ':message', bool $dotNotation = false): array
    {
        $messages = $this->messages;
        $results = [];
        foreach ($messages as $key => $keyMessages) {
            $message = array_shift($messages[$key]);

            if (! $message) {
                continue;
            }

            if ($dotNotation) {
                $results[$key] = $this->formatMessage($message, $format);
            } else {
                Helper::arraySet($results, $key, $this->formatMessage($message, $format));
            }
        }

        return $results;
    }

    /**
     * Get plain array messages
     *
     * @return array<string, array<string, string>>
     */
    public function toArray(): array
    {
        return $this->messages;
    }

    /**
     * Parse $key to get the array of $key and $ruleName
     *
     * @return array{0:string, 1:string|null}
     */
    protected function parseKey(string $key): array
    {
        $expl = explode(':', $key, 2);
        $key = $expl[0];
        $ruleName = $expl[1] ?? null;

        return [$key, $ruleName];
    }

    /**
     * Check the $key is wildcard
     */
    protected function isWildcardKey(string $key): bool
    {
        return str_contains($key, '*');
    }

    /**
     * Filter messages with wildcard key
     *
     * @param  null|string  $ruleName
     * @return array<string, array<string, string>>
     */
    protected function filterMessagesForWildcardKey(string $key, $ruleName = null): array
    {
        $messages = $this->messages;
        $pattern = preg_quote($key, '#');
        $pattern = str_replace('\*', '.*', $pattern);

        $filteredMessages = [];

        foreach ($messages as $k => $keyMessages) {
            if ((bool) preg_match('#^'.$pattern.'\z#u', (string) $k) === false) {
                continue;
            }

            foreach ($keyMessages as $rule => $message) {
                if ($ruleName && $rule != $ruleName) {
                    continue;
                }

                $filteredMessages[$k][$rule] = $message;
            }
        }

        return $filteredMessages;
    }

    /**
     * Get formatted message
     */
    protected function formatMessage(string $message, string $format): string
    {
        return str_replace(':message', $message, $format);
    }
}
