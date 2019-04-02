<?php

declare(strict_types=1);

namespace SimpleYaml;

final class Emitter
{
    /**
     * default inherit for child block
     */
    private const CHILD_BLOCK_INHERIT = 2;

    private $lines = [];

    /**
     * @var Yaml
     */
    private $yaml;

    /**
     * @param Yaml|array $input
     */
    public function __construct($input)
    {
        if ($input instanceof Yaml) {
            $this->yaml = $input;
        } else {
            $this->yaml = new Yaml();
            $this->yaml->setParsedData($input);
        }
    }

    /**
     * @return string
     */
    public function emit()
    {
        $array = $this->yaml->getDataWithAliases();

        foreach ($array as $key => $item) {
            $this->lines = array_merge(
                $this->lines,
                $this->emitBlock(
                    $key,
                    $item
                )
            );
        }

        return implode(PHP_EOL, $this->lines);
    }

    /**
     * @param int $inherit
     *
     * @return string
     */
    private function transformInheritToLine(int $inherit = 0): string
    {
        $string = '';
        for ($i = 0; $i < $inherit; $i++) {
            $string .= ' ';
        }

        return $string;
    }

    /**
     * @param string $key
     * @param string|array $item
     * @param int $keyInherit
     *
     * @return array
     */
    private function emitBlock(string $key, $item, int $keyInherit = 0): array
    {
        $keyInheritString = $this->transformInheritToLine($keyInherit);

        $lines = [];

        if (is_string($item)) {
            $lines[] = $keyInheritString . $key . ': ' . $item;

            return $lines;
        }

        $lines[] = $keyInheritString . $key . ":";

        $valuesInherit = $keyInherit + self::CHILD_BLOCK_INHERIT;

        if (array_keys($item) === range(0, count($item) - 1)) {
            $valuesInheritString = $this->transformInheritToLine($valuesInherit);

            foreach($item as $value) {
                $lines[] = $valuesInheritString . '- ' . $value;
            }

            return $lines;
        }

        foreach($item as $itemKey => $itemVal) {
            $parsedBlock = $this->emitBlock($itemKey, $itemVal, $valuesInherit);

            foreach($parsedBlock as $line) {
                $lines[] = $line;
            }
        }

        return $lines;
    }
}
