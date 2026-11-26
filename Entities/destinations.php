
<?php

class Destination
{
    private ?int $id = null;
    private string $location;
    private float $price;
    private int $tourOperatorId;

    public function __construct(string $location, float $price, int $tourOperatorId){
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

    
    public static function getAll(PDO $db): array{
        
        $request = $db->query('SELECT * FROM destination');

        $results = $request->fetchAll(PDO::FETCH_ASSOC);

        $destinations = [];

        foreach ($results as $result){

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

    public static function getById(PDO $db, int $id): ?Destination{

        $request = $db->prepare(
            'SELECT * FROM destination WHERE id = ?'
        );
        $request->execute([$id]);
        $result = $request->fetch(PDO::FETCH_ASSOC);

        if(!$result){
            return null;
        }

        $destination = new Destination(
            $result['location'],
            $result['price'],
            $result['tour_operator_id']
        );
        $destination->id = $result['id'];
        return $destination;

    }

    public static function getLocations(PDO $db): array
    {
        $request = $db->query('SELECT DISTINCT location FROM destination ORDER BY location');
        $rows = $request->fetchAll(PDO::FETCH_ASSOC);
        $locations = [];
        foreach ($rows as $row) {
            $locations[] = $row['location'];
        }
        return $locations;
    }

    public static function getAllByTourOperator(PDO $db, int $tourOperatorId): array{
        $request = $db->prepare(
            'SELECT * FROM destination WHERE tour_operator_id = ?'
        );

        $request->execute([$tourOperatorId]);

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

    public static function getAllByLocation(PDO $db, string $location): array
    {
        $request = $db->prepare('SELECT * FROM destination WHERE location = ?');
        $request->execute([$location]);
        $results = $request->fetchAll(PDO::FETCH_ASSOC);

        $destinations = [];

        foreach ($results as $result) {
            $destination = new Destination(
                $result['location'],
                (float) $result['price'],
                (int) $result['tour_operator_id']
            );
            $destination->id = (int) $result['id'];
            $destinations[] = $destination;
        }

        return $destinations;
    }

    public function create(PDO $db) : bool{
        $request = $db->prepare(
            'INSERT INTO destination (location, price, tour_operator_id)
             VALUES (:location, :price, :tour_operator_id)'
        );

        $result = $request->execute([
            'location'=> $this->location,
            'price'=> $this->price,
            'tour_operator_id'=> $this->tourOperatorId,
        ]);
         
        if ($result) {
        $this->id = (int) $db->lastInsertId();
    }

        return $result;
    }
}
