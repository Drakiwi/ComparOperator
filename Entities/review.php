<?php
// Objet simple : un avis (ses donnees, sans logique)
class Review
{
    private string $message;
    private string $author;
    private int $tourOperatorId;

    public function __construct(string $message, string $author, int $tourOperatorId)
    {
        $this->message = $message;
        $this->author = $author;
        $this->tourOperatorId = $tourOperatorId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getTourOperatorId(): int
    {
        return $this->tourOperatorId;
    }
}
