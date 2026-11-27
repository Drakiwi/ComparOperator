<?php
// Objet simple : une destination (ses donnees, sans logique)
class Destination
{
    private string $location;
    private float $price;
    private int $tourOperatorId;

    public function __construct(string $location, float $price, int $tourOperatorId)
    {
        $this->location = $location;
        $this->price = $price;
        $this->tourOperatorId = $tourOperatorId;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getTourOperatorId(): int
    {
        return $this->tourOperatorId;
    }
}
