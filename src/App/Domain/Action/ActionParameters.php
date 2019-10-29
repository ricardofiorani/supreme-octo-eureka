<?php declare(strict_types=1);

namespace App\Domain\Action;

use App\Slack\Messages\SlackMentionMessage;
use App\WitAI\Domain\EntitiesCollection;
use App\WitAI\Domain\Response;

class ActionParameters
{
    private Response $aiResponse;
    private SlackMentionMessage $slackMessage;

    public function __construct(Response $AiResponse, SlackMentionMessage $slackMessage)
    {
        $this->aiResponse = $AiResponse;
        $this->slackMessage = $slackMessage;
    }

    public function getAiResponse(): Response
    {
        return $this->aiResponse;
    }

    public function getUser(): string
    {
        return $this->slackMessage->getUser();
    }

    public function getEntities(): EntitiesCollection
    {
        return $this->aiResponse->getEntities();
    }

    public function getSlackMessage(): SlackMentionMessage
    {
        return $this->slackMessage;
    }
}
