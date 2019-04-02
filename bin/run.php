<?php

/**
 * =========================================
 * =                EXAMPLE                =
 * =========================================
 */

include_once __DIR__ . '/../src/autoloader.php';

$parser = new SimpleYaml\Parser();

$parser->parseFile(__DIR__ . '/../tmp/test.yml');

$yaml = $parser->getYamlObject();

var_dump($yaml->getDataWithAliases());
var_dump($yaml->getAliases());
var_dump($yaml->getDataWithoutAliases());

$emitter = new SimpleYaml\Emitter($yaml);
$toFile = $emitter->emit();
var_dump($toFile);
file_put_contents(__DIR__ . '/../tmp/testResult.yml', $toFile);
