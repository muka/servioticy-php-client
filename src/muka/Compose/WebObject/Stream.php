<?php

namespace muka\Compose\WebObject;

/**
 * Description of Stream
 *
 * @author l
 */
class Stream implements \muka\Compose\IValidator
{

    protected $channelTypes = ['number', 'string', 'boolean', 'geo_point'];


    protected $so;

    protected $__name;
    protected $__currentValue;
    protected $__lastUpdate;
    protected $__data;

    private $fields = array();
    private $channels;

    public function __construct($name, array $data = array(), \muka\Compose\IComposeObject $so) {

        $this->__name = $name;
        $this->so = $so;
        $this->channels = new \PhpCollection\Map;

        $this->__lastUpdate = time();

        $this->__currentValue = array(
            "lastUpdate" => $this->getLastUpdate(),
            "channels" => array()
        );

        if($data) {
            foreach($data as $key => $value) {
                $this->fields[$key] = $value;
                if($key == "channels" && count($value)) {
                    $this->addChannels($value);
                }
            }
        }
    }

    public function addChannels($channels) {
        foreach($channels as $name => $fields) {
            $this->addChannel($name, $fields);
        }
        return $this;
    }

    public function addChannel($name, $fields) {
        if(3 == count($args = func_get_args())) {
            $name = array_shift($args);
            $fields = [
              "type" => $args[0],
              "unit" => $args[1],
            ];
        }
        $this->channels->set($name, $fields);
        return $this;
    }

    public function set($name, $value = null) {
        if(is_array($name)) {
            foreach($name as $k => $v) {
                $this->set($k, $v);
            }
        }
        else {
            $this->fields[$name] = $value;
        }
        return $this;
    }

    public function __set($name, $value) {
        $this->fields[$name] = $value;
    }

    public function __get($name) {
        return isset($this->fields[$name]) ? $this->fields[$name] : null;
    }

    public function getChannels() {
        return $this->channels;
    }

    public function getChannel($name) {
        return $this->channels->get($name)->getOrElse(null);
    }

    public function toJson($asText = false) {

        $object = new \stdClass();

        foreach($this->fields as $key => $value) {
            $object->{$key} = $value;
        }

        $object->channels = [];
        foreach($this->channels as $name => $channel) {
            $object->channels[$name] = $channel;
        }

        return $asText ? json_encode($object) : $object;
    }


    public function __toString() {
        return $this->toJson(true);
    }

    public function getName() {
        return $this->__name;
    }

    public function setData(array $data) {
        return $this->__data = $data;
    }

    public function getData() {
        return $this->__data;
    }

    public function clearData() {
        $this->__data = [];
    }

    public function getLastUpdate() {
        return $this->__lastUpdate;
    }

    public function setLastUpdate($time = null) {
        if(is_null($time)) {
            $time = time();
        }
        $this->__lastUpdate = $time;
        $this->__setLastUpdated();
        return $this;
    }

    public function setValue($channelName, $value) {
        if($channel = $this->channels->get($channelName)) {
            $this->__currentValue['channels'][$channelName]['current-value'] = $value;
            if($value === null) {
                unset($this->__currentValue['channels'][$channelName]);
            }
        }
        return $this;
    }

    public function setValues(array $values) {
        foreach($values as $channelName => $value) {
            $this->setValue($channelName, $value);
        }
        return $this;
    }

    public function getValue($channelName) {
        if($channel = $this->channels->get($channelName)
                && isset($this->__currentValue['channels'][$channelName]['current-value'])) {
            return $this->__currentValue['channels'][$channelName]['current-value'];
        }
        return null;
    }

    public function getValues() {
        $this->__setLastUpdated();
        return $this->__currentValue;
    }

    protected function __setLastUpdated() {
        $this->__currentValue['lastUpdate'] = $this->__lastUpdate;
    }

    public function isValid() {

        if(!$this->getName()) {
            return false;
        }

        if(!$this->getChannels()) {
            return false;
        }

        foreach ($this->getChannels() as $channel) {

            if(!isset($channel->type) || !isset($channel->unit)) {
                return false;
            }

            if(!$channel->type || !$channel->unit) {
                return false;
            }

            if(!in_array($channel->type, $this->channelTypes)) {
                return false;
            }

        }

        return true;
    }

}
