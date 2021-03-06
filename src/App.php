<?php declare(strict_types=1);

namespace Hawk;

use Hawk\Psr7\Factory\ServerRequestFactory;
use League\Route\Route;
use League\Route\RouteGroup;
use League\Route\Router;
use League\Route\RouteCollectionInterface;
use League\Route\Strategy\JsonStrategy;
use League\Route\Strategy\StrategyInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Class App
 * @package Hawk
 */
class App
{
    /**
     *
     */
    public const VERSION = '0.0.1';

    /**
     * @var ResponseFactoryInterface
     */
    protected $response;

    /**
     * @var ContainerInterface|null
     */
    protected $container;

    /**
     * @var Router|null
     */
    protected $router;

    /**
     * App constructor.
     * @param ResponseFactoryInterface $response
     * @param ContainerInterface|null $container
     * @param RouteCollectionInterface|null $router
     */
    public function __construct(
        ResponseFactoryInterface $response,
        ?ContainerInterface $container = null,
        ?RouteCollectionInterface $router = null
    )
    {
        $this->response = $response;
        $this->container = $container;
        $this->router = $router ?? new Router();
    }

    /**
     * @return ResponseFactoryInterface
     */
    public function getResponse(): ResponseFactoryInterface
    {
        return $this->response;
    }

    /**
     * @return ContainerInterface|null
     */
    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    /**
     * @param StrategyInterface $strategy
     */
    public function setStrategy(StrategyInterface $strategy)
    {
        $this->router->setStrategy($strategy);
    }

    /**
     * @return StrategyInterface|null
     */
    public function getStrategy()
    {
        return $this->router->getStrategy();
    }

    /**
     * @param string $path
     * @param $handler
     * @return Route|null
     */
    public function get(string $path, $handler)
    {
        return $this->map(['GET'], $path, $handler);
    }

    /**
     * @param string $path
     * @param $handler
     * @return Route|null
     */
    public function post(string $path, $handler)
    {
        return $this->map('POST', $path, $handler);
    }

    /**
     * @param string $path
     * @param $handler
     * @return Route|null
     */
    public function put(string $path, $handler)
    {
        return $this->map('PUT', $path, $handler);
    }

    /**
     * @param string $path
     * @param $handler
     * @return Route|null
     */
    public function path(string $path, $handler)
    {
        return $this->map('PATH', $path, $handler);
    }

    /**
     * @param string $path
     * @param $handler
     * @return Route|null
     */
    public function delete(string $path, $handler)
    {
        return $this->map('DELETE', $path, $handler);
    }

    /**
     * @param string $path
     * @param $handler
     * @return Route|null
     */
    public function options(string $path, $handler)
    {
        return $this->map('OPTIONS', $path, $handler);
    }

    /**
     * @param string $prefix
     * @param callable $group
     * @return RouteGroup
     */
    public function group(string $prefix, callable $group)
    {
        return $this->router->group($prefix, $group);
    }

    /**
     * @param array|string $method
     * @param string $path
     * @param callable|string $handler
     * @return Route|null
     */
    public function map($method, string $path, $handler)
    {
        if (!is_string($method)) {
            throw new \InvalidArgumentException("The parameter method must have a string type");
        }

        $route = $this->router->map($method, $path, $handler);

        return $route;
    }

    /**
     *
     */
    public function emit()
    {
        $request = ServerRequestFactory::fromGlobals(
            $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
        );

        $response = $this->router->dispatch($request);

        (new ResponseEmitter())->emit($response);
    }

    /**
     *
     */
    public function json()
    {
        $this->setStrategy(new JsonStrategy($this->response));
        $this->emit();
    }

    /**
     * @param string $alias
     * @param string $regex
     * @return Router
     */
    public function addPattern(string $alias, string $regex)
    {
        return $this->router->addPatternMatcher($alias, $regex);
    }

    /**
     * @param MiddlewareInterface $middleware
     * @return \League\Route\Middleware\MiddlewareAwareInterface
     */
    public function middleware(MiddlewareInterface $middleware)
    {
        return $this->router->middleware($middleware);
    }

}
