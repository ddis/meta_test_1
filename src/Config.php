<?php


namespace Ddis\Meta;

/**
 * Class Config
 *
 * @package Ddis\Meta
 */
class Config
{
    private $rules = [];

    /**
     * @param string $openTag
     * @param string $closeTag
     *
     * @return Config
     */
    public function setRules(string $openTag, string $closeTag): Config
    {
        $this->rules[$openTag] = $closeTag;

        return $this;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return Config
     */
    public static function getDefaultConfig()
    {
        return (new self())->setRules("{", "}")
                           ->setRules("[", "]")
                           ->setRules("(", ")");
    }
}
