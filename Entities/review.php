<?php

class Review{

    private int $id = null;
    private string $author;
    private string $message;
    private int $tourOperatorId;

    public function__construct(string $author, string $message, int $tourOperatorId){
        $this->author = $author;
        $this->message = $message;
        $this->tourOperatorId = $tourOperatorId;
    }

    public function getId(): int{
        return $this->id;
    }

    public function getAuthor(): string{
        return $this->author;
    }

    public function getMessage(): string{
        return $this->message;
    }

    public function getTourOperatorId(): int{
        return $this->getTourOperatorId;
    }

}