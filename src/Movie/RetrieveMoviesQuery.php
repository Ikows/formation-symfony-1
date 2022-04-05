<?php

namespace App\Movie;

class RetrieveMoviesQuery
{
    private $movies = [];

    public function setMovies(array $movies): void
    {
        $this->movies = $movies;
    }

    public function getMovies(): array
    {
        return $this->movies;
    }
}