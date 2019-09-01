<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-29 09:38:00
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-06-19 10:09:38
 */
namespace App\Http\Response\Traits;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

trait ResponseTrait
{
    private $statusCode = HttpResponse::HTTP_OK;
    private $response;
    /**
     * set http status code.
     *
     * @param int $status
     * @return $this
     */
    public function setStatusCode($status)
    {
        $this->statusCode = $status;
        return $this;
    }
    /**
     * get http status code.
     *
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    /**
     * return  response json data
     * @param  $data
     * @param  array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseJson($data, array $headers = [])
    {
        return response()->json($data, $this->statusCode, $headers);
    }
    /**
     * return  response json message
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseMessage($message)
    {
        return response()->json([
            'message'=>$message
        ], $this->statusCode);
    }
    /**
     * return 201 response with the given created resource.
     * @param   $data
     * @return  \Illuminate\Http\JsonResponse
     */
    public function withCreated($data)
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_CREATED
        )->responseJson($data);
    }
    /**
     * return 204 no content response.
     * @return    \Illuminate\Http\JsonResponse
     */
    public function withNotContent()
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_NO_CONTENT
        )->responseJson([]);
    }
    /**
     * return 205 with reset resource no content response.
     * @return    \Illuminate\Http\JsonResponse
     */
    public function withResetContent($message)
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_RESET_CONTENT
        )->responseJson(['message'=>$message]);
    }
    /**
     * Make a 401 'Unauthorized' response.
     *
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function withUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_UNAUTHORIZED
        )->responseMessage($message);
    }
    /**
     * return 410 response with resource perpetual gone.
     *
     * @return    \Illuminate\Http\JsonResponse
     */
    public function withGone($message = 'success delete resource')
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_NO_CONTENT
        )->responseMessage($message);
    }
    /**
     * return 501 response request Not Implemented.
     *
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function withNotImplemented($message)
    {
        return $this->setStatusCode(
            HttpResponse::HTTP_NOT_IMPLEMENTED
        )->responseMessage($message);
    }
}