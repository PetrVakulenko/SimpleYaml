<?php

include_once __DIR__ . '/../src/autoloader.php';

$yamlReader = new SimpleYaml\Reader();

$yamlReader->parseFile(__DIR__ . '/../tmp/test.yml');

var_dump($yamlReader->getParsedData());
