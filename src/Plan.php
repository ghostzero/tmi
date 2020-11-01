<?php

namespace GhostZero\Tmi;

class Plan
{
    public bool $prime;
    public string $plan;
    public string $name;

    public function __construct(bool $prime, string $plan, string $name)
    {
        $this->prime = $prime;
        $this->plan = $plan;
        $this->name = $name;
    }
}
