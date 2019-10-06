<?php declare(strict_types=1);

namespace App\Messages;

class SlackMentionMessage
{
    private string $type;
    private string $user;
    private string $text;
    private string $ts;
    private string $channel;
    private string $event_ts;

    public function __construct(string $type, string $user, string $text, string $ts, string $channel, string $event_ts)
    {
        $this->type = $type;
        $this->user = $user;
        $this->text = $text;
        $this->ts = $ts;
        $this->channel = $channel;
        $this->event_ts = $event_ts;
    }

    public static function createFromArray(array $array): self
    {
        return new self(
            $array['type'],
            $array['user'],
            $array['text'],
            $array['ts'],
            $array['channel'],
            $array['event_ts'],
        );
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getTs(): string
    {
        return $this->ts;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function getEventTs(): string
    {
        return $this->event_ts;
    }
}
