<?php

namespace App\Traits;

trait HasRatings
{
    public function getRatingPercentage($star)
    {
        $total = $this->reviews()->approved()->count();
        if ($total === 0) {
            return 0;
        }

        $count = $this->reviews()->approved()->where('rating', $star)->count();
        return ($count / $total) * 100;
    }

    public function getRatingDistribution()
    {
        return [
            5 => $this->getRatingPercentage(5),
            4 => $this->getRatingPercentage(4),
            3 => $this->getRatingPercentage(3),
            2 => $this->getRatingPercentage(2),
            1 => $this->getRatingPercentage(1),
        ];
    }
}
