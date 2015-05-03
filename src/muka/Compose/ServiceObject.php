<?php

namespace muka\Compose;

use muka\Compose\Api\Client;

/**
 * Description of ServiceObject
 *
 * @author l
 */
class ServiceObject extends WebObject
{

    protected $__client;
    protected $__streamClass = ServiceObject\Stream::class;

    function __construct($obj = null) {
        parent::__construct($obj);
        $this->__client = new Client($this);
    }

    /**
     * Creates a service object based on its definition
     */
    public function create() {

        $result = $this->getClient()->post("/", $this->toJson());

        if(isset($result['id'])) {
            $this->id = $result['id'];
            $this->createdAt = $result['createdAt'];
        }
        else {
            throw new Api\Exception\ComposeException("Platform response doesn't contain a Service Object ID");
        }

        return $this;
    }

    /**
     * Load the definition of a service object based on its id
     *
     * @param string $id An (optional) SO id to load, otherwise the object id property will be used
     */
    public function read($id = null) {

        if($id) {
            $this->id = $id;
        }

        if(!$this->hasId()) {
            $this->error("Service Object id not set");
        }

        $result = $this->getClient()->get(array("/%s", $this->id));
        $this->load($result);

        return $this;
    }

    /**
     * Get all the created SO by the API key in use
     *
     * @return array A list of SO id
     */
    public function index() {
        return $this->getClient()->get("/");
    }

    /**
     * Updates a SO definition
     *
     * @todo Update when this will be implemented
     *
     * @return ServiceObject The instance of the SO
     */
    public function update() {

        if(!$this->hasId()) {
            $this->error("Service Object id not set");
        }

        $this->getClient()->put(array("/%s", $this->id), $this->toJson());
        return $this;
    }

    /**
     * Delete a SO definition
     *
     * @todo Update when this will be implemented, if ever
     *
     * @return ServiceObject The instance of the SO, without the id
     */
    public function delete() {

        if(!$this->hasId()) {
            $this->error("Service Object id not set");
        }

        $this->getClient()->delete(array("/%s", $this->id));
        $this->id = null;

        return $this;
    }

    /**
     * @return boolean Check if the SO has an id
     */
    public function hasId() {
        return $this->id;
    }

    /**
     * Return the http API client
     *
     * @return muka\Compose\Api\Client the http API client
     */
    public function getClient() {
        return $this->__client;
    }

    public function setApiKey($k) {
        $this->getClient()->setApiKey($k);
    }

    public function setBaseUrl($u) {
        $this->getClient()->setBaseUrl($u);
    }

}
