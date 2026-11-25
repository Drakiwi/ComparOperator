
<?php

class Review{
    private ?int $id = null;
    private string $message;
    private string $author;
    private int $tourOperatorId;

    public function __construct(string $message, string $author, int $tourOperatorId){
        $this->message = $message;
        $this->author = $author;
        $this->tourOperatorId = $tourOperatorId;
    }

    public function getId(): ?int{
        return $this->id;
    }

    public function getMessage(): string{
        return $this->message;
    }

    public function getAuthor(): string{
        return $this->author;
    }

    public function getTourOperatorId(): int{
        return $this->tourOperatorId;
    }

    public static function getAllByTourOperator(PDO $db, int $tourOperatorId): array{
        $request = $db->prepare(
            'SELECT * FROM review WHERE tour_operator_id = ?'
        );
        $request->execute([$tourOperatorId]);
        $results = $request->fetchAll(PDO::FETCH_ASSOC);
        $reviews = [];

        foreach($results as $result){
            $review = new Review(
                $result['message'],
                $result['author'],
                $result['tour_operator_id']
            );
            $review->id = $result['id'];
            $reviews[] = $review;

        }
        return $reviews;
    }
    public function create(PDO $db): bool{
        $request = $db->prepare(
            'INSERT INTO review (message, author, tour_operator_id)
            VALUES (:message, :author, :tour_operator_id)'
        );

        $result = $request->execute([
            'message' => $this->message,
            'author' => $this->author,
            'tour_operator_id' => $this->tourOperatorId
        ]);

        if ($result) {
            $this->id = (int) $db->lastInsertId();
        }

        return $result;
    }
}
