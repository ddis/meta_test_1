<?php


namespace Ddis\Meta;

use Exception;

/**
 * Class Validator
 *
 * @package Ddis\Meta
 */
class Validator
{
    protected $rules = [];

    protected $openTags  = [];
    protected $closeTags = [];

    protected $regexp = "";
    protected $string = "";

    private $pool = [];

    /**
     * Validator constructor.
     *
     * @param Config|null $config
     */
    public function __construct(Config $config = null)
    {
        $this->rules = $this->setRules($config);

        $this->openTags  = array_keys($this->rules);
        $this->closeTags = array_values($this->rules);

        $this->regexp = $this->buildRegexp();
    }

    /**
     * @param string $string
     *
     * @return Validator
     */
    public function setString(string $string): Validator
    {
        $this->string = $string;

        return $this;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function validate(): bool
    {
        preg_match_all($this->regexp, $this->string, $items);

        if (!$items[0]) {
            throw new Exception("Symbols not found");
        }

        if (count($items[0]) % 2) {
            return false;
        }

        return $this->checkItems($items[0]);
    }

    /**
     * @param array $items
     *
     * @return bool
     * @throws Exception
     */
    private function checkItems(array $items): bool
    {
        $status = true;

        foreach ($items as $item) {
            switch (true) {
                case $this->isOpenTag($item):
                    $this->addToPool($this->rules[$item]);
                    break;
                case $this->isCloseTag($item):
                    if (!$this->removeFromPool($item)) {
                        $status = false;
                        break 2;
                    }
                    break;
                default:
                    throw new Exception("Undefined type");
            }
        }

        return (!$this->pool && $status);
    }

    /**
     * @param string $item
     */
    private function addToPool(string $item)
    {
        array_unshift($this->pool, $item);
    }

    /**
     * @param string $tag
     *
     * @return bool
     */
    private function removeFromPool(string $tag): bool
    {
        if (!$this->pool || $this->pool[0] != $tag) {
            return false;
        }

        array_shift($this->pool);

        return true;
    }

    /**
     * @param string $tag
     *
     * @return bool
     */
    private function isOpenTag(string $tag): bool
    {
        return in_array($tag, $this->openTags);
    }

    /**
     * @param string $tag
     *
     * @return bool
     */
    private function isCloseTag(string $tag): bool
    {
        return in_array($tag, $this->closeTags);
    }

    /**
     * @param Config|null $config
     *
     * @return array
     */
    private function setRules(Config $config = null): array
    {
        if (!$config) {
            return Config::getDefaultConfig()->getRules();
        }

        return $config->getRules();
    }

    /**
     * @return string
     */
    private function buildRegexp(): string
    {
        return "/[\\" . implode("\\", $this->openTags) . "\\" . implode("\\", $this->closeTags) . "]+/mU";
    }
}
