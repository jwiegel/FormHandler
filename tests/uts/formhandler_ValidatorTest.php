<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;

require_once __DIR__ . '/../../src/includes/class.Validator.php';

/**
 * The validators of class.Validator.php, called like Field::isValid() does: on an instance.
 * Every validator "IsX" has an optional variant "_IsX" that also accepts an empty value.
 */
final class formhandler_ValidatorTest extends FormhandlerTestCase
{
    /**
     * @return array<string, array{string, string, bool}> validator, value, expected
     */
    public static function values(): array
    {
        $cases = [
            'IsString' => [['text', true], ['with space', true], ["line\nbreak", false], ["tab\t", false]],
            'IsAlpha' => [['abcXYZ', true], ['abc1', false], ['ab c', false], ['äöü', false]],
            'IsDigit' => [['0123', true], ['12a', false], ['-1', false], ['1.5', false]],
            'IsAlphaNum' => [['abc123', true], ['abc_123', false], ['abc 123', false]],
            'IsFloat' => [['1.5', true], ['-1.5', true], ['1,5', true], ['.5', true], ['10', true], ['1.5.5', false], ['abc', false]],
            'IsInteger' => [['42', true], ['-42', true], ['4.2', false], ['4a', false]],
            'IsFilename' => [['file.txt', true], ['my file.txt', true], ['dir/file', false], ['a*b', false], ['a?b', false], ['a:b', false]],
            'IsVariabele' => [['name', true], ['_name1', true], ['_', false], ['1name', false], ['my-name', false]],
            'IsPassword' => [['Secret!123', true], ['with space', false], ['ümlaut', false]],
            'IsURL' => [['example.org', true], ['www.example.org', true], ['https://www.example.org', true], ['https://example.org/path?x=1', true], ['example', false], ['exa mple.org', false]],
            'IsEmail' => [['anna@example.org', true], ['anna.meyer@sub.example.co.uk', true], ['anna@', false], ['@example.org', false], ['anna@example', false], ['an na@example.org', false]],
            'IsText' => [['text', true], ["line\nbreak\r\n\ttab", true], ["bell\x07", false]],
            'IsPostcode' => [['1234 AB', true], ['1234AB', true], ['0123 AB', false], ['12345', false]],
            'IsPhone' => [['0201234567', true], ['020-1234567', true], ['020 123 45 67', true], ['12345', false], ['02012345678', false]],
            'IsIp' => [['192.168.0.1', true], ['192.168.0.1:8080', true], ['192.168.0', false], ['192.168.0x1', false], ['256.1.1.1', false]],
            'NoHTML' => [['plain text', true], ['<b>bold</b>', false], ['<script>x</script>', false], ['', false]],
            'IsBool' => [['true', true], ['FALSE', true], ['1', true], ['0', true], ['yes', false], ['10', false], ['1abc', false]],
        ];

        $data = [];
        foreach ($cases as $validator => $values)
        {
            foreach ($values as [$value, $expected])
            {
                $data["$validator('$value')"] = [$validator, $value, $expected];
            }
        }
        return $data;
    }

    #[DataProvider('values')]
    public function test_validator(string $validator, string $value, bool $expected): void
    {
        static::assertSame($expected, (bool) (new Validator())->$validator($value));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function optionalValidators(): array
    {
        $names = [];
        foreach (get_class_methods('Validator') as $method)
        {
            if (preg_match('/^_(Is\w+|NoHTML)$/', $method) && $method !== '_IsEmailHost' && $method !== '_IsURLHost')
            {
                $names[$method] = [$method];
            }
        }
        return $names;
    }

    #[DataProvider('optionalValidators')]
    public function test_optionalValidatorAcceptsEmpty(string $validator): void
    {
        static::assertTrue((bool) (new Validator())->$validator(''));
    }

    #[DataProvider('values')]
    public function test_optionalValidatorLikeRequired(string $validator, string $value, bool $expected): void
    {
        if ($value === '')
        {
            // the optional validators accept an empty value, see test_optionalValidatorAcceptsEmpty()
            $expected = true;
        }
        static::assertSame($expected, (bool) (new Validator())->{'_' . $validator}($value));
    }

    public function test_notEmpty(): void
    {
        $v = new Validator();
        static::assertTrue($v->notEmpty('x'));
        static::assertTrue($v->notEmpty(['x']));
        static::assertFalse($v->notEmpty(''));
        static::assertFalse($v->notEmpty('   '));
        static::assertFalse($v->notEmpty([]));
    }

    public function test_isBoolKeepsValue(): void
    {
        $value = 'false';
        static::assertTrue(Validator::IsBool($value));
        static::assertFalse($value, 'IsBool() converts "false" to false');
    }

    public function test_staticCall(): void
    {
        // Validator::IsEmail() is used like the other validators
        static::assertTrue((bool) Validator::IsEmail('anna@example.org'));
        static::assertTrue((bool) Validator::_IsURL(''));
    }
};
