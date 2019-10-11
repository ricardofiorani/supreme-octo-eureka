<?php declare(strict_types=1);

namespace App\WitAI\Domain;

use App\WitAI\Factory\EntitiesCollectionFactory;
use Psr\Http\Message\ResponseInterface;

class Response
{
    private string $text;
    private EntitiesCollection $entities;
    private string $messageId;

    private function __construct(string $text, EntitiesCollection $entities, string $messageId)
    {
        $this->text = $text;
        $this->entities = $entities;
        $this->messageId = $messageId;
    }

    public static function createFromPsrResponse(ResponseInterface $response): self
    {
        $responseBody = json_decode((string)$response->getBody(), true);

        return new self(
            $responseBody['_text'],
            EntitiesCollectionFactory::createFromArray($responseBody['entities']),
            $responseBody['msg_id'],
        );
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getEntities(): EntitiesCollection
    {
        return $this->entities;
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function toArray(): array
    {
        return [
            'text' => $this->getText(),
            'entities' => $this->getEntities()->toArray(),
            'messageId' => $this->getMessageId(),
        ];
    }
}
