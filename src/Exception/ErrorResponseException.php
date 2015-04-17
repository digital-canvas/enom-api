<?php namespace DigitalCanvas\Enom\Exception;


use GuzzleHttp\Message\ResponseInterface;

class ErrorResponseException extends \RuntimeException
{

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}
