<?php

declare(strict_types=1);

namespace Nayan\Router\Interfaces;

/**
 * Interface IResponse
 *
 * This interface defines the methods that a response object must implement.
 *
 * @package Router
 * @author Nayan Adhikari
 * @version 1.0.0
 * @license MIT
 * @link
 * @see
 */
interface IResponse
{
    /**
     * This method sets the content of the response.
     *
     * @param string $content The content of the response.
     * @return IResponse The response object.
     */
    public function withView(string $view, array $content = []): IResponse;
    
    /**
     * This method sets the status code of the response.
     *
     * @param int $statusCode The status code of the response.
     * @return IResponse The response object.
     */
    public function withStatusCode(int $statusCode): IResponse;
    
    /**
     * This method adds a header to the response.
     *
     * @param string $name The name of the header.
     * @param string $value The value of the header.
     * @return IResponse The response object.
     */
    public function withHeader(string $name, string $value): IResponse;

    /**
     * This method sets the content of the response to JSON.
     *
     * @param mixed $content The content of the response.
     * @return IResponse The response object.
     */
    public function withJson(mixed $content): IResponse;
    
    /**
     * This method returns the content of the response.
     *
     * @return mixed The content of the response.
     */
    public function getContent(): mixed;
    
    /**
     * This method returns the status code of the response.
     *
     * @return int The status code of the response.
     */
    public function getStatusCode(): int;
    
    /**
     * This method returns a header value by name.
     *
     * @return string header by name
     */
    public function getHeader(string $name): string;

    /**
     * This method returns all headers.
     *
     * @return array All headers.
     */
    public function getHeaders(): array;

    /**
     * This method sends the response.
     */
    public function respond(): string;
}
