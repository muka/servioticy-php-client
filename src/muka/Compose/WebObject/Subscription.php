<?php

namespace muka\Compose\WebObject;

/**
 * @author l
 */
class Subscription
{

    protected $so;

    private $fields = [];

    private $defaultFields = [
        "id"            => null,
        "createdAt"     => null,
        "updatedAt"     => null,
        "callback"      => "pubsub",
        "source"        => null,
        "stream"        => null,
        "customFields"  => []
    ];

    public function __construct(array $data, \muka\Compose\IComposeObject $so) {

        $this->so = $so;

        if(!is_array($data)) {
            $data = [];
        }

        if($data) {
            foreach($data as $key => $value) {
                $this->fields[$key] = $value;
            }
        }
    }

    public function __set($name, $value) {
        $this->fields[$name] = $value;
    }

    public function __get($name) {
        return isset($this->fields[$name]) ? $this->fields[$name] : null;
    }

    public function toJson($asText = false) {

        $object = new \stdClass();

        foreach($this->fields as $key => $value) {
            $object->{$key} = $value;
        }

        return $asText ? json_encode($object) : $object;
    }

    public function __toString() {
        return $this->toJson(true);
    }

    public function getName() {
        return $this->name;
    }

}
