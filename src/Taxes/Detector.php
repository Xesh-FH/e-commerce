<?php

namespace App\Taxes;

class Detector
{
    protected float $seuil;

    public function __construct(float $seuil)
    {
        $this->seuil = $seuil;
    }

    /**
     * @var float $amount
     * @return bool
     */
    public function detect(float $amount): bool
    {
        return $amount > $this->seuil;
    }
}
