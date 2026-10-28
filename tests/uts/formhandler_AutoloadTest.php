<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;

final class formhandler_AutoloadTest extends FormhandlerTestCase
{
    public static function dataClasses(): array
    {
        $classes = array();
        foreach (array('buttons', 'fields', 'includes', 'db') as $dir)
        {
            foreach (glob(dirname(__DIR__, 2) . "/src/$dir/*.php") as $file)
            {
                if (preg_match('/^(?:class|interface)\.(\w+)\.php$/', basename($file), $match))
                {
                    $classes[$match[1]] = array($match[1]);
                }
            }
        }

        return $classes;
    }

    /**
     * Every class is loaded by Composer's autoloader, before any form method
     * needed it (projects extend e.g. TextArea in their own field classes)
     */
    #[DataProvider('dataClasses')]
    public function test_autoload(string $class): void
    {
        static::assertTrue(
            class_exists($class) || interface_exists($class),
            "$class is not autoloaded"
        );
    }
}
