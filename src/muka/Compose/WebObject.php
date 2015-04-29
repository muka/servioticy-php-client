<?php

namespace muka\Compose;

use PhpCollection\Map;

/**
 * Description of WebObject
 *
 * @author l
 */
class WebObject implements IComposeObject
{

    private $handledProperties = ['streams', 'actions', 'subscriptions'];
    private $fields = [];

    protected $streams;
    protected $actions;
    public $customFields;
    public $properties;

    protected $__streamClass = '\muka\Compose\WebObject\Stream';

    // shared local repository where to find objects definitions
    public static $localRepositoryPath = './definitions';

    function __construct($json = null) {

        $this->streams       = new Map;
        $this->actions       = new Map;
        $this->subscriptions = new Map;

        $this->properties = [];
        $this->customFields = new \stdClass();

        if($json) {
            $this->load($json);
        }

    }

    /**
     * Load a mixed source to a WebObject / SErviceObjct
     */
    public function load($source) {

        $parseJson = function($str) {
            $json = json_decode($str, false);
            if($json == null) {
                throw new Exception\ParserException("Error parsing JSON, syntax is not correct");
            }
            return $json;
        };

        if(is_object($source) || is_array($source)) {
            return $this->loader($source);
        }

        // Check if it is available in the local repository
        $filename = realpath(self::$localRepositoryPath) ."/{$source}.json";

        if(file_exists($filename)) {
            $source = $filename;
        }

        // parse from file or url
        if(file_exists($source) || @parse_url($source)) {

            // Silent warnings, evaluate the return value only
            $content = @file_get_contents($source);
            if($content !== false) {
                $json = $parseJson($content);
            }
            else {
                throw new Exception\ParserException(sprintf("Unable to read JSON from %s", $source));
            }
        }
        else {
            // parse as JSON string
            $json = $parseJson($json);
        }

        return $this->loader($json);
    }

    protected function loader($json) {

        $handledProperties = $this->handledProperties;

        foreach($json as $key => $value) {

            if(!in_array($key, $handledProperties)) {
                $this->fields[$key] = $value;
                continue;
            }

            foreach ($value as $objKey => $objValue) {
                switch($key) {
                    case "streams":
                        $this->addStream($objKey, (array)$objValue);
                        break;
                    case "actions":
                        $this->addAction((array)$objValue);
                        break;
                    case "subscriptions":
                        $this->addSubscription((array)$objValue);
                        break;
                }
            }
        }

        return $this;
    }

    protected function getFields() {
        return $this->fields;
    }

    public function __set($name, $value) {
        $this->fields[$name] = $value;
    }

    public function __get($name) {
        return (isset($this->fields[$name])) ? $this->fields[$name] : null;
    }

    /**
     * @param string $key The stream id
     * @param array $value An array of key/value pairs for the stream
     *
     * @return WebObject\Stream The stream created
     */
    public function addStream($key, $value = []) {
        $value = new $this->__streamClass($key, $value, $this);
        $this->streams->set($key, $value);
        return $this->getStream($key);
    }

    /**
     * @param string $name The stream id
     *
     * @return WebObject\Stream The corresponding stream or null
     */
    public function getStream($name) {
        return $this->streams->get($name)->getOrElse(null);
    }

    public function getStreams() {
        return $this->streams;
    }

    public function addAction($key, $value = []) {
        $value = new ServiceObject\Action($value, $this);
        $this->actions->set($value->getName(), $value);
        return $value;
    }

    public function getAction($name) {
        return $this->actions->get($name)->getOrElse(null);
    }

    public function getActions() {
        return $this->actions;
    }

    public function addSubscription(array $value = []) {
        $value = new ServiceObject\Subscription($value, $this);
        $this->actions->set($value->getName(), $value);
        return $value;
    }

    public function getSubscription($name) {
        return $this->subscriptions->get($name)->getOrElse(null);
    }

    public function getSubscriptions() {
        return $this->subscriptions;
    }

    public function getProperties() {
        return $this->properties;
    }

    public function getCustomFields() {
        return $this->customFields;
    }

    public function __toString() {
        return $this->toJson(true);
    }

    public function toJson($asText = false) {

        $object = [];

        foreach($this->getFields() as $key => $value){
            if($key == 'data') {
                continue;
            }
            $object[ $key ] = $value;
        }

        $object['customFields'] = $this->customFields;
        $object['properties'] = $this->properties;

        $handledProperties = $this->handledProperties;

        foreach ($handledProperties as $key) {

            $object[ $key ] = [];

            foreach($this->{$key} as $name => $elem) {
                $object[ $key ][ $name ] = $elem->toJson();
            }

        }
        return $asText ? json_encode($object) : $object;
    }

}
