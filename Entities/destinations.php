
<?php

class Destination
{
    private ?int $id = null;
    private string $location;
    private float $price;
    private int $tourOperatorId;

    public function __construct(
        string $location,
        float $price,
        int $tourOperatorId
    ) {
        $this->location = $location;
        $this->price = $price;
        $this->tourOperatorId = $tourOperatorId;
    }

    public function getId(): ?int
    {
        return $this->id;
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
