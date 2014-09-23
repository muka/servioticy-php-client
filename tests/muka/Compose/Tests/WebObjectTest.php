<?php

namespace muka\Compose\Tests;

use muka\Compose\WebObject;

/**
 * Description of WebObjectTest
 *
 * @author l
 */
class WebObjectTest extends \PHPUnit_Framework_TestCase
{
    /** @var WebObject */
    private $wo;

    private $definitions = array(
      "good"        => './tests/definitions/smartphone.json',
      "bad"    => './tests/definitions/bad-json.json',
      "not_found"   => './this/definition/does/not/exists.json',
    );

    public function testLoad()
    {
        $json = $this->definitions["good"];
        $obj = json_decode(file_get_contents($json), false);
        $wo = new WebObject($json);

        $this->assertTrue($wo->name == $obj->name);
        $this->assertTrue($wo->getStream('location')->getChannel('latitude')->type ==
                                $obj->streams->location->channels->latitude->type);

        $this->assertTrue($wo->getStream('address_book')->description == $obj->streams->address_book->description);
    }

    public function testLoadRepo()
    {
        // load from local repository eg ./definitions (the value of WebObject::$localRepositoryPath)
        $json = "smartphone";

        $wo = new WebObject($json);

        $path = realpath(WebObject::$localRepositoryPath);
        $obj = json_decode(file_get_contents("{$path}/{$json}.json"), false);

        $this->assertTrue($wo->name == $obj->name);
        $this->assertTrue($wo->getStream('location')->getChannel('latitude')->type ==
                                $obj->streams->location->channels->latitude->type);

        $this->assertTrue($wo->getStream('address_book')->description == $obj->streams->address_book->description);
    }

    /**
     * @expectedException muka\Compose\Exception\ParserException
     */
    public function testLoadErrorNotFound()
    {
        new WebObject($this->definitions['not_found']);
    }

    /**
     * @expectedException muka\Compose\Exception\ParserException
     */

    public function testLoadErrorBadJson()
    {
        new WebObject($this->definitions['bad']);
    }

    public function testCreate()
    {

        $this->wo = $wo = new WebObject;

        $wo->name = "MyPhone";
        $wo->description = "A smartphone web object";
        $wo->URL = "compose://example.com";

        $wo->properties['uuid'] = '1111-2222-3333-4444';

        $wo->addStream("gps")
          ->set([
            "description" => "Phone location",
            "type" => "sensor"
          ])
          ->addChannel("latitude", [
            "type" => "float",
            "unit" => "degrees",
          ])
          ->addChannel("longitude", "float", "degrees")
        ;

        $accel = $wo->addStream("accelerometer");
        $accel->description = "Phone location";
        $accel->type = "sensor";

        $accel->addChannel("x", [
            "type" => "float",
            "unit" => "degrees",
        ]);
        $accel->addChannels([
            "y" => [
                "type" => "float",
                "unit" => "degrees",
            ],
            "z" => [
                "type" => "float",
                "unit" => "degrees",
            ],
        ]);

        $this->assertNotNull(array($wo, 'type'));
        $this->assertNotNull(array($wo->getStream('gps'), 'description'));

        $this->assertTrue(2 == $wo->getStreams()->count());
        $this->assertTrue(3 == $wo->getStream('accelerometer')->getChannels()->count());

        $this->assertTrue(null == $wo->getStream('accelerometer')->getChannel("k"));

        $this->assertTrue(is_string("$wo"));

    }

}
