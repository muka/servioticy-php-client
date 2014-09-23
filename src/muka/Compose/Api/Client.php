<?php

namespace muka\Compose\Api;

/**
 * Description of Client
 *
 * @author l
 */
class Client
{
    protected $http;
    protected $headers;

    public static $baseUrl;
    public static $apiKey;

    private $__lastResponse = null;

    function __construct() {}

    protected function beforeRequest($type, &$args) {
        $args['url'] = $this->url($args['url']);
        $args['headers'] = $this->headers($args['headers']);
    }

    protected function afterRequest(&$response) {

    }

    protected function request($type, $args) {
        $this->beforeRequest($type, $args);
        $response = call_user_func_array(array($this->http(), $type), $args);
        $this->afterRequest($response);
        return $response;
    }

    /**
     * Perform a GET request
     *
     * @todo Handle better the creation of querystring
     *
     * @param string $url base path to the endpoint
     * @param array $data Key/value list of data to send, if any
     * @param array $headers Key/value list of headers
     */
    public function get($url, array $data = array(), array $headers = array()) {
        if($data) {
            $url .= '?'.http_build_query($data);
            $data = array();
        }
        return $this->request("get", compact("url", "data", "headers"));
    }

    /**
     * Perform a POST request
     *
     * @param string $url base path to the endpoint
     * @param array $data Key/value list of data to send, if any
     * @param array $headers Key/value list of headers
     */
    public function post($url, $data, array $headers = array()) {
        return $this->request("post", compact("url", "data", "headers"));
    }

    /**
     * Perform a PUT request
     *
     * @param string $url base path to the endpoint
     * @param array $data Key/value list of data to send, if any
     * @param array $headers Key/value list of headers
     */
    public function put($url, $data, array $headers = array()) {
        return $this->request("put", compact("url", "data", "headers"));
    }

    /**
     * Perform a DELETE request
     *
     * @param string $url base path to the endpoint
     * @param array $headers Key/value list of headers
     */
    public function delete($url, array $headers = array()) {
        return $this->request("delete", compact("url", "headers"));
    }

    protected function error($message) {
        throw new Exception\ClientException($message);
    }

    protected function url($url) {
        return (is_array($url)) ? call_user_func_array("sprintf", $url) : $url;
    }

    protected function headers($headers = array()) {

        if(!$this->headers) {

            if(!self::$apiKey) {
                $this->error("Client::apiKey needs to be set before making requests");
            }

            $this->headers['Authorization'] = self::$apiKey;
        }

        return array_merge($this->headers, $headers);
    }

    protected function http() {
        if(is_null($this->http)) {

            // basic validation
            if(!self::$baseUrl) {
                $this->error("Client::baseUrl needs to be set before making requests");
            }

            $this->http = new \PestJSON(self::$baseUrl);
        }

        return $this->http;
    }

    /**
     *
     * @return mixed The last request response as object, if any
     */
    public function getLastResponse() {
        return $this->__lastResponse;
    }

    public static function setBaseUrl($baseUrl) {
        self::$baseUrl = $baseUrl;
    }
    public static function setApiKey($apiKey) {
        self::$apiKey = $apiKey;
    }
}