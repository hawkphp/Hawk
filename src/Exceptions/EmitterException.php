<?php declare(strict_types=1);

namespace Hawk\Exception;

use RuntimeException;

/**
 * Class EmitterException
 * @package Hawk\Exception
 */
class EmitterException extends RuntimeException
{
    /**
     * @return EmitterException
     */
    public static function forHeadersSent(): self
    {
        return new self('Unable to emit response; headers already sent');
    }

    /**
     * @return EmitterException
     */
    public static function forOutputSent(): self
    {
        return new self('Output has been emitted previously; cannot emit response');
    }
}