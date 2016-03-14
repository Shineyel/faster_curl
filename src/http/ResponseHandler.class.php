<?php
namespace http;
interface ResponseHandler {
    /**
     * Processes an HttpResponse and returns some value
     * corresponding to that response.
     *
     * @param CurlResponse The response to process
     * @return CurlResponse value determined by the response
     */
    public function handleResponse(CurlResponse $response);
}