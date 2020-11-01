<?php

namespace GhostZero\Tmi\Traits;

trait HasTagSignature
{
    public function signature(): ?string
    {
        return $this->tags['id'] ?? null;
    }
}
