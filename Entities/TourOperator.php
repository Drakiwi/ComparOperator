<?php

class TourOperator
{
    private ?int $id = null;
    private string $name;
    private string $link;
    private int $gradeCount;
    private int $gradeTotal;
    private bool $isPremium;

    public function __construct(string $name, string $link, int $gradeCount = 0, int $gradeTotal = 0, bool $isPremium = false)
    {
        $this->name = $name;
        $this->link = $link;
        $this->gradeCount = $gradeCount;
        $this->gradeTotal = $gradeTotal;
        $this->isPremium = $isPremium;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getGradeCount(): int
    {
        return $this->gradeCount;
    }

    public function getGradeTotal(): int
    {
        return $this->gradeTotal;
    }

    public function isPremium(): bool
    {
        return $this->isPremium;
    }

    // Note moyenne (0 si aucun avis)
    public function getGrade(): float
    {
        if ($this->gradeCount === 0) {
            return 0;
        }
        return $this->gradeTotal / $this->gradeCount;
    }

    // Toutes les TO
    public static function getAll(PDO $db): array
    {
        $request = $db->query('SELECT * FROM tour_operator');
        $results = $request->fetchAll(PDO::FETCH_ASSOC);

        $tourOperators = [];
        foreach ($results as $result) {
            $tourOperator = new TourOperator(
                $result['name'],
                $result['link'],
                $result['grade_count'],
                $result['grade_total'],
                (bool) $result['is_premium']
            );
            $tourOperator->id = $result['id'];
            $tourOperators[] = $tourOperator;
        }
        return $tourOperators;
    }

    public static function getById(PDO $db, int $id): ?TourOperator
    {
        $request = $db->prepare('SELECT * FROM tour_operator WHERE id = ?');
        $request->execute([$id]);
        $result = $request->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        $tourOperator = new TourOperator(
            $result['name'],
            $result['link'],
            $result['grade_count'],
            $result['grade_total'],
            (bool) $result['is_premium']
        );
        $tourOperator->id = $result['id'];
        return $tourOperator;
    }

    // Creer un nouveau TO
    public function create(PDO $db): bool
    {
        $request = $db->prepare(
            'INSERT INTO tour_operator (name, link, grade_count, grade_total, is_premium)
             VALUES (:name, :link, :grade_count, :grade_total, :is_premium)'
        );
        $result = $request->execute([
            'name' => $this->name,
            'link' => $this->link,
            'grade_count' => $this->gradeCount,
            'grade_total' => $this->gradeTotal,
            'is_premium' => (int) $this->isPremium,
        ]);

        if ($result) {
            $this->id = (int) $db->lastInsertId();
        }
        return $result;
    }

    // Passer en premium (ou non)
    public function setPremium(PDO $db, bool $isPremium): bool
    {
        $request = $db->prepare(
            'UPDATE tour_operator SET is_premium = :is_premium WHERE id = :id'
        );
        $result = $request->execute([
            'is_premium' => (int) $isPremium,
            'id' => $this->id,
        ]);

        if ($result) {
            $this->isPremium = $isPremium;
        }
        return $result;
    }

    // Ajouter une note (augmente la moyenne)
    public function addGrade(PDO $db, int $grade): bool
    {
        $this->gradeCount++;
        $this->gradeTotal += $grade;

        $request = $db->prepare(
            'UPDATE tour_operator SET grade_count = :gc, grade_total = :gt WHERE id = :id'
        );
        $result = $request->execute([
            'gc' => $this->gradeCount,
            'gt' => $this->gradeTotal,
            'id' => $this->id,
        ]);
        return $result;
    }

    // Destinations d'un TO
    public function getDestinations(PDO $db): array
    {
        if ($this->id === null) {
            return [];
        }
        return Destination::getAllByTourOperator($db, $this->id);
    }
}