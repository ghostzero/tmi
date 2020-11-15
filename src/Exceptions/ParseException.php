<?php

namespace GhostZero\Tmi\Exceptions;

use DomainException;
use Throwable;

class ParseException extends DomainException
{
    public static function fromParseSingle(string $message, Throwable $throwable): self
    {
        return new self(sprintf('Cannot parse `%s`.', $message), 0, $throwable);
    }
}