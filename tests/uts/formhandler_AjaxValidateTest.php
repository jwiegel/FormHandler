<?php

declare(strict_types=1);

/**
 * The answer of src/FHTML/ajax/validate.php (AjaxValidator::Validate()). The request
 * comes from the browser, so it must not bring own HTML or javascript into the answer.
 */
final class formhandler_AjaxValidateTest extends FormhandlerTestCase
{
    private function request(array $request): string
    {
        $validator = new AjaxValidator();
        return (string) $validator->Validate($request + array(
            'validator' => 'IsDigit',
            'msg'       => 'Only digits',
            'msgbox'    => 'error_age',
            'value'     => 'abc',
        ), new Validator());
    }

    public function test_invalidValue(): void
    {
        $answer = $this->request(array());

        static::assertStringContainsString("jQuery('#error_age').addClass( 'fh_error' );", $answer);
        static::assertStringEndsWith('</script>Only digits', $answer);
    }

    public function test_validValue(): void
    {
        $answer = $this->request(array('value' => '42'));

        static::assertStringContainsString("jQuery('#error_age').addClass( 'fh_ok' );", $answer);
    }

    public function test_messageIsEscaped(): void
    {
        $answer = $this->request(array('msg' => '<img src=x onerror=alert(1)>'));

        static::assertStringEndsWith('</script>&lt;img src=x onerror=alert(1)&gt;', $answer);
    }

    public function test_msgboxIsChecked(): void
    {
        static::assertSame('', $this->request(array('msgbox' => "error_age').html('<b>x</b>');//")));
        static::assertSame('', $this->request(array('msgbox' => 'age')));
    }

    public function test_missingParameter(): void
    {
        $validator = new AjaxValidator();

        static::assertSame('', $validator->Validate(array('validator' => 'IsDigit'), new Validator()));
    }

    public function test_endpointIgnoresIncludedir(): void
    {
        // a separate process: validate.php loads the classes itself
        $request = array(
            'includedir' => '/does/not/exist/',
            'validator'  => 'IsDigit',
            'msg'        => 'Only digits',
            'msgbox'     => 'error_age',
            'value'      => 'abc',
        );
        $code = '$_REQUEST = ' . var_export($request, true) . '; include ' . var_export(dirname(__DIR__, 2) . '/src/FHTML/ajax/validate.php', true) . ';';

        $answer = (string) shell_exec(escapeshellarg(PHP_BINARY) . ' -n -r ' . escapeshellarg($code) . ' 2>&1');

        static::assertStringContainsString("jQuery('#error_age').addClass( 'fh_error' );", $answer);
        static::assertStringEndsWith('</script>Only digits', $answer);
    }
}
