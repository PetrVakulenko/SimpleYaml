<?php

declare(strict_types=1);

namespace SimpleYaml;

final class Parser
{
    /**
     * @var array
     */
    private $lines = [];

    /**
     * @var array
     */
    private $lineInherits = [];

    /**
     * @var Yaml
     */
    public $yaml;

    public function __construct()
    {
        $this->yaml = new Yaml();
    }

    /**
     * @param string $filePath
     */
    public function parseFile(string $filePath): void
    {
        $this->parseLines($filePath);

        $rootLies = $this->getRootLines();

        foreach ($rootLies as $lineIndex) {
            $this->yaml->dataWithAliases = array_merge(
                $this->yaml->dataWithAliases,
                $this->parseBlock($lineIndex)
            );
        }

        $this->yaml->replaceAliases();
    }

    /**
     * @return Yaml
     */
    public function getYamlObject(): Yaml
    {
        return $this->yaml;
    }

    /**
     * Recursive block parser.
     *
     * @param int $lineIndex
     * @param bool $getAliasData
     *
     * @return array
     */
    private function parseBlock(int $lineIndex): array
    {
        $lineParts = explode(": ", $this->lines[$lineIndex]);

        $lineParts[0] = $this->prepareLine($lineParts[0]);

        if ($lineIndex == $this->getEndBlockLine($lineIndex)) {
            $this->checkAliasData($lineParts[0], trim($lineParts[1]));

            $lineParts[0] = $this->prepareAliasData($lineParts[0]);

            return [$lineParts[0] => trim($lineParts[1])];
        }

        $childLines = $this->getChildLines($lineIndex);
        if (!$this->childsHasDepth($childLines)) {
            if (count($childLines) == 1) {
                $this->checkAliasData($lineParts[0], trim($this->lines[$childLines[0]]));

                $lineParts[0] = $this->prepareAliasData($lineParts[0]);

                return [$lineParts[0] => trim($this->lines[$childLines[0]])];
            }

            return [$lineParts[0] => $this->getChildLinesWithoutDepth($childLines)];
        }

        $result = [];

        foreach ($childLines as $childLine) {
            $childValue = $this->parseBlock($childLine);

            if (is_array($childValue)) {
                $result = array_merge(
                    $result,
                    $childValue
                );
            }
        }

        return [$lineParts[0] => $result];
    }

    private function prepareAliasData(string $val): string
    {
        if (substr($val, 0, 1) === '&') {
            return substr($val, 1);
        }

        return $val;
    }

    /**
     * @param string $key
     * @param string $val
     */
    private function checkAliasData(string $key, string $val): void
    {
        if (substr($key, 0, 1) === '&') {
            $this->yaml->putAlias($key, $val);
        }
    }

    /**
     * Return lines for left inherit = 0
     *
     * @return array
     */
    private function getRootLines(): array
    {
        $rootLines = [];

        for ($i = 0; $i < count($this->lineInherits); $i++) {
            if ($this->lineInherits[$i] == 0) {
                $rootLines[] = $i;
            }
        }

        return $rootLines;
    }

    /**
     * Getting child lines (without depth)
     *
     * @param int $line
     *
     * @return array
     */
    private function getChildLines(int $line): array
    {
        $endBlockLine = $this->getEndBlockLine($line);

        if ($endBlockLine == $line) {
            return [];
        }

        $childsInherit = $this->lineInherits[$line+1];

        $childs = [];

        for ($i = $line+1; $i <= $endBlockLine; $i++) {
            if ($childsInherit == $this->lineInherits[$i]) {
                $childs[] = $i;
            }
        }

        return $childs;
    }

    /**
     * Getting end of block, which is beginning from current lineIndex
     *
     * @param int $lineIndex
     *
     * @return int
     */
    private function getEndBlockLine(int $lineIndex): int
    {
        for ($i = $lineIndex+1; $i < count($this->lineInherits); $i++){
            if ($this->lineInherits[$i] <= $this->lineInherits[$lineIndex]) {
                return $i - 1;
            }
        }

        return count($this->lineInherits) - 1;
    }

    /**
     * @param string $line
     *
     * @return string
     */
    private function prepareLine(string $line): string
    {
        $line = trim($line);
        $line = rtrim($line, ':');
        $line = ltrim($line, '- ');

        return $line;
    }

    /**
     * @param array $childLines
     *
     * @return array
     */
    private function getChildLinesWithoutDepth(array $childLines): array
    {
        $result = [];

        foreach ($childLines as $lineIndex) {
            $result[] = ltrim(trim($this->lines[$lineIndex]), '- ');
        }

        return $result;
    }

    /**
     * Checking if childs has depth
     *
     * @param array $childLines
     *
     * @return bool
     */
    private function childsHasDepth(array $childLines): bool
    {
        foreach ($childLines as $lineIndex) {
            if ($this->getEndBlockLine($lineIndex) > $lineIndex
                || strpos($this->lines[$lineIndex], ': ') !== false
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Function is parsing .yml file and filling params lines and inherits.
     *
     * @param string $filePath
     */
    private function parseLines(string $filePath): void
    {
        $f = fopen($filePath, 'r');

        $lineIndex = 0;

        while ($string = fgets($f)) {
            if (trim($string) == '') continue;

            $this->lines[$lineIndex] = $string;
            $this->lineInherits[$lineIndex] = strlen($string) - strlen(ltrim($string));

            $lineIndex++;
        }
    }
}
