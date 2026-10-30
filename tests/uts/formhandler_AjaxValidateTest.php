<?php

declare(strict_types=1);

/**
 * The answer of src/FHTML/ajax/validate.php (AjaxValidator::Validate()): JSON, which
 * ajax_validator.js shows as text, so the request can not bring HTML into the page.
 */
final class formhandler_AjaxValidateTest extends FormhandlerTestCase
{
    /**
     * @return array<string, mixed>|null
     */
    private function request(array $request): ?array
    {
        $validator = new AjaxValidator();
        $answer = (string) $validator->Validate($request + array(
            'validator' => 'IsDigit',
            'msg'       => 'Only digits',
            'value'     => 'abc',
        ), new Validator());

        return $answer === '' ? null : json_decode($answer, true);
    }

    public function test_invalidValue(): void
    {
        static::assertSame(array('status' => 'error', 'msg' => 'Only digits'), $this->request(array()));
    }

    public function test_validValue(): void
    {
        static::assertSame(array('status' => 'ok', 'msg' => ''), $this->request(array('value' => '42')));
    }

    public function test_emptyValue(): void
    {
        static::assertSame(array('status' => 'mandatory', 'msg' => ''), $this->request(array('validator' => '_IsDigit', 'value' => '')));
    }

    public function test_moreValidators(): void
    {
        // every validator is checked, not only the first one
        static::assertSame('error', $this->request(array('validator' => 'IsString|IsDigit'))['status']);
    }

    public function test_wrongRequest(): void
    {
        $validator = new AjaxValidator();

        static::assertSame('', $validator->Validate(array('validator' => 'IsDigit'), new Validator()), 'missing parameter');
        static::assertNull($this->request(array('validator' => 'NoSuchValidator')));
        static::assertNull($this->request(array('validator' => 'FH_CAPTCHA')));
    }

    public function test_endpointIgnoresIncludedir(): void
    {
        // a separate process: validate.php loads the classes itself
        $request = array(
            'includedir' => '/does/not/exist/',
            'validator'  => 'IsDigit',
            'msg'        => 'Only digits',
            'value'      => 'abc',
        );
        $code = '$_REQUEST = ' . var_export($request, true) . '; include ' . var_export(dirname(__DIR__, 2) . '/src/FHTML/ajax/validate.php', true) . ';';

        $answer = (string) shell_exec(escapeshellarg(PHP_BINARY) . ' -n -r ' . escapeshellarg($code) . ' 2>&1');

        static::assertSame('{"status":"error","msg":"Only digits"}', $answer);
    }
}
