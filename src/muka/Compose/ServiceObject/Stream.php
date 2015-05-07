<?php

/*
 * The MIT License
 *
 * Copyright 2014 luca capra <luca.capra@create-net.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace muka\Compose\ServiceObject;

/**
 * Description of Stream
 *
 * @author l
 */
class Stream extends \muka\Compose\WebObject\Stream
{

    protected $__client;

    public function __construct($name, array $data = [], \muka\Compose\IComposeObject $so) {
        parent::__construct($name, $data, $so);
        $this->__client = new \muka\Compose\Api\Client;
    }

    public function pull($timeModifier = "") {

        if(!$this->so->hasId()) {
            $this->error("Service Object id not set");
        }

        $response = $this->getClient()->get(["/%s/streams/%s%s",$this->so->id,$this->getName(),($timeModifier ? "/$timeModifier": "")]);

        if(isset($response['data'])) {
            $this->setData($response['data']);
        }

        return $this;
    }

    public function push(array $data = array()) {

        if(!$this->so->hasId()) {
            $this->error("Service Object id not set");
        }

        if($data) {
            $this->setValues($data);
        }

        $response = $this->getClient()->put(["/%s/streams/%s", $this->so->id, $this->getName()], $this->getValues());

        return $this;
    }

    /**
     * Return the http API client
     *
     * @return muka\Compose\Api\Client the http API client
     */
    public function getClient() {
        return $this->__client;
    }

}
