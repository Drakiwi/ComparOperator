<?php
// Objet simple : un tour operateur (ses donnees, sans logique)
class TourOperator
{
    private string $name;
    private string $link;
    private int $gradeCount;
    private int $gradeTotal;
    private bool $isPremium;

    public function __construct(string $name, string $link, int $gradeCount, int $gradeTotal, bool $isPremium)
    {
        $this->name = $name;
        $this->link = $link;
        $this->gradeCount = $gradeCount;
        $this->gradeTotal = $gradeTotal;
        $this->isPremium = $isPremium;
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
}
