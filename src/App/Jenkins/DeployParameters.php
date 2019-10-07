<?php declare(strict_types=1);

namespace App\Jenkins;

use App\Slack\Messages\SlackMentionMessage;

class DeployParameters
{
    private string $market;
    private string $environment;
    private string $branch;
    private SlackMentionMessage $message;

    public function __construct(string $market, string $environment, string $branch, SlackMentionMessage $message)
    {
        $this->market = $market;
        $this->environment = $environment;
        $this->branch = $branch;
        $this->message = $message;
    }

    public static function create(array $array, SlackMentionMessage $message): self
    {
        return new self(
            $array['entities']['market_entity'][0]['value'],
            $array['entities']['environment_entity'][0]['value'],
            $array['entities']['branch_entity'][0]['value'],
            $message,
        );
    }

    public function getMarket(): string
    {
        return $this->market;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getBranch(): string
    {
        return $this->branch;
    }

    public function getMessage(): SlackMentionMessage
    {
        return $this->message;
    }
}
