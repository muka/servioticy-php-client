<?php

namespace muka\Compose\Tests;

use muka\Compose\ServiceObject;

/**
 * Description of WebObjectTest
 *
 * @author l
 */
class ServiceObjectTest extends \PHPUnit_Framework_TestCase
{

    private $definition = './tests/definitions/smartphone.json';
    private $endpointBaseUrl = 'http://127.0.0.1:8080/api-public';
    private $endpointApiKey = '21276359507f4059f607';

    protected function setUp() {
        ServiceObject::setApiKey($this->endpointApiKey);
        ServiceObject::setBaseUrl($this->endpointBaseUrl);
    }

    public function testCreate()
    {
        $so = new ServiceObject($this->definition);
        $so->create();

        $this->assertNotNull(array($so, 'id'));
    }

    public function testRead()
    {

        // create new one
        $so1 = new ServiceObject($this->definition);
        $so1->create();
        $soid = $so1->id;

        // create new one
        $so2 = new ServiceObject($this->definition);
        $so2->read($soid);

        $this->assertTrue($so1->name == $so1->name);
        $this->assertTrue($so1->getStreams()->count() == $so1->getStreams()->count());

    }

    /**
     */
    public function testList()
    {

        // create new one
        $so1 = new ServiceObject($this->definition);
        $so1->create();

        // get list
        $list = $so1->index();

//        $this->assertTrue(in_array($so1->id, $list));
    }

    /**
     * @expectedException muka\Compose\Exception\NotImplementedException
     */
    public function testUpdate()
    {

        // create new one
        $so1 = new ServiceObject($this->definition);
        $so1->create();

        $so1->addStream("test", array(
            "name" => "test1",
            "channels" => array(
                "testA" => array(
                    "type" => "Number",
                    "unit" => "items"
                )
            )
        ));

        $so1->update();
    }

    /**
     * @expectedException muka\Compose\Exception\NotImplementedException
     */
    public function testDelete()
    {
        // create new one
        $so1 = new ServiceObject($this->definition);
        $so1->create();
        // bye
        $so1->delete();
    }

    /**
     */
    public function testPushPullData()
    {
        // create new one
        $so1 = new ServiceObject($this->definition);
        $so1->create();

        $so1->getStream("location")
                ->setValues(array(
                    "latitude" => 10.123,
                ))
                ->setValue('longitude', 40.321);

        // mode 1
        $so1->getStream("location")->push();

        // mode 2
        $so1->getStream("location")
            ->setLastUpdate(time() - 200)
            ->push(array(
                "latitude" => 12.321,
                "longitude" => 42.321,
            ));

        $so1->getStream("location")->pull('lastUpdate');
        $this->assertTrue(count($so1->getStream("location")->getData()) == 1);

        $so1->getStream("location")->pull();

        $this->assertTrue(count($so1->getStream("location")->getData()) == 2);

    }

}
