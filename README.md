## Simple YML parser

Simple YML parser and emitter.

- [Requirements](#requirements)
- [Getting application](#getting-application)
- [Example of using](#example-of-using)
    - [Parser](#parser)
    - [Get data_with_aliases](#get-data-with-aliases)
    - [Get data without aliases](#get-data-without-aliases)
    - [Get aliases](#get-aliases)
    - [Emitter](#emitter)

### Requirements

* php 7.2+

### Getting application
Clone repository to the common place:
```bash
git clone git@github.com:PetrVakulenko/SimpleYaml.git ~/code/SimpleYaml/
```

### Example of using

##### Parser
```bash
$parser = new SimpleYaml\Parser();

$parser->parseFile(__DIR__ . '/../tmp/test.yml');
```

##### Get data with aliases
```bash
$array = $parser->getYamlObject()->getDataWithAliases();
```

##### Get data without aliases
```bash
$array = $parser->getYamlObject()->getDataWithoutAliases();
```

##### Get aliases
```bash
$array = $parser->getYamlObject()->getAliases();
```

##### Emitter

```bash
$yaml = $parser->getYamlObject();

$emitter = new SimpleYaml\Emitter($yaml);
$string = $emitter->emit();
```
