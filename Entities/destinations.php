
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

    
    public static function getAll(PDO $db): array
    {
        $request = $db->query('SELECT * FROM destination');

        $results = $request->fetchAll(PDO::FETCH_ASSOC);

        $destinations = [];

        foreach ($results as $result) {

        $destination = new Destination(
            $result['location'],
            $result['price'],
            $result['tour_operator_id']
        );

        $destination->id = $result['id'];

        $destinations[] = $destination;
    }

        return $destinations;
    }
}
