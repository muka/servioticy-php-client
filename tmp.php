<?php

require '/home/vagrant/src/idm/vendor/autoload.php';

$obj = <<<EQ
{
  "name": "Drone",
  "description": "My amazing drone",
  "properties": [],
  "customFields": {
    "model": "drone-001",
    "colors": [
      "red",
      "blue"
    ]
  },
  "streams": [
    {
      "description": "GPS location",
      "type": "sensor",
      "name": "location",
      "channels": {
        "position": {
          "name": "position",
          "unit": "degree",
          "type": "geo_point"
        },
        "altitude": {
          "type": "number",
          "unit": "meter",
          "name": "altitude"
        },
        "heading": {
          "name": "heading",
          "unit": "degree",
          "type": "number"
        }
      }
    }
  ],
  "actions": [
    {
      "name": "turn-right"
    },
    {
      "name": "turn-left"
    },
    {
      "name": "fly-up"
    },
    {
      "name": "fly-down"
    }
  ]
}
EQ;

$so = new muka\Compose\ServiceObject($obj);
echo $so->toJson(true);
