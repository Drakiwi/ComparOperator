<?php

class TourOperator{
    
    private int $id;
    private string $name;
    private float $grade;
    private bool $premium;
    private string $website;
    private array $destinations = [];


    public function__construct(string $name, float $grade, bool $premium = false, string $website = null){

        $this->name = $name;
        $this->grade = $grade;
        $this->premium = $premium;
        $this->website = $website;
    }

    public function getId(): int{
        return $this->id;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getGrade(): float{
        return $this->grade;
    }

    public function isPremium() : bool{
        return $this->premium;
    }

    public function getWebsite(): string{
        return $this->website;
    }

    public function getDestination(): array{
        return $this->destinations;
    }

    public function addDestination(): void{
        $this->destinations[] = $destination;
    }
}