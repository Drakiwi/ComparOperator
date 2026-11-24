<?php

class TourOperator{
    
    private int $id;
    private string $name;
    private int $grade;

    public function__construct(string $name, int $grade){

        $this->name = $name;
        $this->grade = $grade;
    }

    public function getId(): int{
        return $this->id;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getGrade(): int{
        return $this->grade;
    }
}