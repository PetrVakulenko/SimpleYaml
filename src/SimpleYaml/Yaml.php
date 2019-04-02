<?php

declare(strict_types=1);

namespace SimpleYaml;

final class Yaml
{
    private $aliases = [];

    public $dataWithAliases = [];
    public $data = [];

    /**
     * @param string $key
     * @param string $val
     */
    public function putAlias(string $key, string $val): void
    {
        if (substr($key, 0, 1) === '&') {
            $key = substr($key, 1);
        }

        $this->aliases[$key] = $val;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function aliasExists(string $key): bool
    {
        return isset($this->aliases[$key]) && !empty($this->aliases[$key]);
    }

    public function getAliasValue(string $key): string
    {
        if (substr($key, 0, 1) === '*') {
            $key = substr($key, 1);
        }

        return $this->aliases[$key];
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @return array
     */
    public function getDataWithAliases(): array
    {
        return $this->dataWithAliases;
    }

    /**
     * @return array
     */
    public function getDataWithoutAliases(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setParsedData(array $data): void
    {
        $this->dataWithAliases = $data;
    }

    public function replaceAliases(): void
    {
        $data = $this->getDataWithAliases();

        foreach ($data as $key => $item) {
            $data[$key] = $this->replaceAliasesBlock($item);
        }

        $this->data = $data;
    }

    /**
     * @param string|array $item
     *
     * @return array|string
     */
    private function replaceAliasesBlock($item)
    {
        if (is_string($item)) {
            return $this->prepareAliasData($item);
        }

        if (array_keys($item) === range(0, count($item) - 1)) {
            foreach($item as $itemKey => $value) {
                $item[$itemKey] = $this->prepareAliasData($value);
            }

            return $item;
        }

        foreach($item as $itemKey => $itemVal) {
            $item[$itemKey] = $this->replaceAliasesBlock($itemVal);
        }

        return $item;
    }

    /**
     * @param string $item
     *
     * @return string
     */
    private function prepareAliasData(string $item): string
    {
        if (substr($item, 0, 1) === '*') {
            return $this->getAliasValue(substr($item, 1));
        }

        return $item;
    }
}
