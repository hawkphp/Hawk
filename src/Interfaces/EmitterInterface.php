<?php declare(strict_types=1);

namespace Hawk\Interfaces;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface EmitterInterface
 * @package Hawk\Interfaces
 */
interface EmitterInterface
{
    /**
     * @param ResponseInterface $response
     * @return bool
     */
    public function emit(ResponseInterface $response): bool;
}