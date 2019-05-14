<?php

/**
 * Class ValidatorTest
 */
class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    private $rules = [];

    protected function setUp()
    {
        $this->rules = [
            '2*(2-2)-(2+4)' => true,
            '(()))('        => false,
            '()'            => true,
            '{[{(){}}]}'    => true,
            '[{)}(]'        => false,
            '{{[}'          => false,
            '{{'            => false,
            '()()'          => true,
        ];
    }

    /**
     * @throws Exception
     */
    public function testValidate()
    {
        foreach ($this->rules as $rule => $result) {
            $validate = new \Ddis\Meta\Validator();
            $this->assertEquals($result, $validate->setString($rule)->validate());
        }
    }
}
