<?php declare(strict_types=1);

namespace Hawk;

use Hawk\Exception\EmitterException;
use Hawk\Interfaces\EmitterInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseEmitter
 * @package Hawk
 */
class ResponseEmitter implements EmitterInterface
{
    /**
     * @param ResponseInterface $response
     * @return bool
     */
    public function emit(ResponseInterface $response): bool
    {
        $this->assertNoPreviousOutput();
        $this->emitHeaders($response);
        $this->emitStatusLine($response);
        $this->emitBody($response);
        return true;
    }

    /**
     * @param ResponseInterface $response
     */
    private function emitBody(ResponseInterface $response): void
    {
        echo $response->getBody();
    }

    /**
     *
     */
    private function assertNoPreviousOutput()
    {
        if (headers_sent()) {
            throw EmitterException::forHeadersSent();
        }
        if (ob_get_level() > 0 && ob_get_length() > 0) {
            throw EmitterException::forOutputSent();
        }
    }

    /**
     * @param ResponseInterface $response
     */
    private function emitStatusLine(ResponseInterface $response): void
    {
        $reasonPhrase = $response->getReasonPhrase();
        $reasonPhrase = ($reasonPhrase ? ' ' . $reasonPhrase : '');
        $statusCode = $response->getStatusCode();
        header(sprintf('HTTP/%s %d%s', $response->getProtocolVersion(), $statusCode, $reasonPhrase), true, $statusCode);
    }

    /**
     * @param ResponseInterface $response
     */
    private function emitHeaders(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();
        foreach ($response->getHeaders() as $header => $values) {
            $name = $this->filterHeader($header);
            $first = $name === 'Set-Cookie' ? false : true;
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), $first, $statusCode);
                $first = false;
            }
        }
    }

    /**
     * @param string $header
     * @return string
     */
    private function filterHeader(string $header): string
    {
        return ucwords($header, '-');
    }
}