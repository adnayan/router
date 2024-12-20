<?php

declare(strict_types=1);

namespace Nayan\Router\Interfaces;

/**
 * Interface IRequest
 *
 * This interface defines the methods that a request object must implement.
 *
 * @package Router
 * @author Nayan Adhikari
 * @version 1.0.0
 * @license MIT
 * @link
 * @see
 */
interface IRequest
{
    /**
     * This method adds a header to the request.
     *
     * @param string $name The name of the header.
     * @param string $value The value of the header.
     * @return void
     */
    public function addHeader(string $name, string $value): void;
    
    /**
     * This method sets the method of the request.
     *
     * @param string $method The method of the request.
     * @return void
     */
    public function getMethod(): string;
    
    /**
     * This method sets the method of the request.
     *
     * @param string $method The method of the request.
     * @return void
     */
    public function getHeader(string $name): array;
    
    /**
     * This method sets the method of the request.
     *
     * @param string $method The method of the request.
     * @return void
     */
    public function getQuery(string $key, $default = null);
    
    /**
     * This method sets the method of the request.
     *
     * @param string $method The method of the request.
     * @return void
     */
    public function getQueries(): array;
    
    /**
     * This method sets the method of the request.
     *
     * @param string $method The method of the request.
     * @return void
     */
    public function getBody(): string;
    
    /**
     * This method sets the method of the request.
     *
     * @param string $method The method of the request.
     * @return void
     */
    public function getServer(string $key);
}
