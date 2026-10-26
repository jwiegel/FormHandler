<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

$_SERVER['REQUEST_METHOD'] = 'POST';

// for testen trigger_error
define('FH_DISPLAY_ERRORS', false);
// for fewer text in unittests
define('FH_DEFAULT_ROW_MASK', "%title%%seperator%%field%%help%%error_id%%error%");
define('FH_DEFAULT_GLUE_MASK', "%field%");
define('FH_FIELDSET_MASK', "BEGIN%name%%extra%%caption%%content%END");

abstract class FormhandlerTestCase extends TestCase
{
    private ?ReflectionClass $_Reflector = null;

    private array $_triggeredErrors = array();

    protected function assertTriggertError(string $message, int $errorlevel): void
    {
        static::assertTrue(count($this->_triggeredErrors) > 0, "no errors triggered");

        $triggeredError = array_shift($this->_triggeredErrors);
        static::assertEquals($triggeredError["level"], $errorlevel, "wrong expected errorlevel");
        static::assertEquals($triggeredError["message"], $message, "wrong expected errormessage");
    }

    public function errorHandler(int $errorlevel, string $message)
    {
        array_push($this->_triggeredErrors, array("level" => $errorlevel, "message" => $message));
    }

    protected function setUp(): void
    {
        set_error_handler(array($this, 'errorhandler'));
    }

    protected function tearDown(): void
    {
        if (count($this->_triggeredErrors) > 0)
        {
            $triggeredErrormessage = $this->_triggeredErrors[0]["message"];
            static::fail("Unexpected triggered error: {$triggeredErrormessage}");
        }
        restore_error_handler();
    }

    protected function getFormhandlerType(): string
    {
        return "Formhandler";
    }

    private function getReflector(): ReflectionClass
    {
        if ($this->_Reflector === null)
            $this->_Reflector = new ReflectionClass($this->getFormhandlerType());
        return $this->_Reflector;
    }

    /**
     * getPrivateProperty
     *
     * @param FormHandler $form
     * @param string $propertyName
     * @return mixed
     */
    public function getPrivateProperty(FormHandler $form, string $propertyName)
    {
        $property = $this->getReflector()->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($form);
    }

    /**
     * setPrivateProperty
     *
     * @param FormHandler $form
     * @param string $propertyName
     * @return mixed
     */
    public function setPrivateProperty(FormHandler $form, string $propertyName, $value): void
    {
        $property = $this->getReflector()->getProperty($propertyName);
        $property->setAccessible(true);

        $property->setValue($form, $value);
    }

    /**
     * executePrivateMethod
     *
     * @param FormHandler $form
     * @param string $propertyName
     * @param array $params
     * @return mixed
     */
    public function executePrivateMethod(FormHandler $form, string $methodName, array $params)
    {
        $method = $this->getReflector()->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($form, $params);
    }

    /**
     * Check for HTML-Form output
     *
     * @param FormHandler $theForm
     * @param string|array $expected
     * @return string HTML for additional validation
     */
    protected static function assertFormFlushContains(FormHandler $form, $expected): string
    {
        $t = (string)$form->flush(true);
        if (is_array($expected))
        {
            // ordercheck
            $lastPos = -1;
            foreach ($expected as $e)
            {
                //static::assertStringContainsString($e, $t);
                $p = strpos($t, $e);
                if (!$p)
                {
                    static::fail("missing string:\n{$e}");
                }

                static::assertGreaterThan($lastPos, $p, "wrong order of strings '{$e}'");
                $lastPos = $p;
            }
        }
        else
            static::assertStringContainsString($expected, $t);

        return (string)$t;
    }

    /**
     * Flush after post is correct
     *
     * @param FormHandler $form
     * @return void
     */
    protected static function assertFlush(FormHandler $form): void
    {
        $t = $form->flush(true);
        static::assertEquals("", $t);
    }
};
