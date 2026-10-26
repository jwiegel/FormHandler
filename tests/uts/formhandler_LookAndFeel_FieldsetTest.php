<?php

declare(strict_types=1);

final class formhandler_LookAndFeel_FieldsetTest extends FormhandlerTestCase
{
    public function test(): void
    {
        $form = new FormHandler();

        $form->borderStart();
        $form->borderStop();

        static::assertFormFlushContains($form, "BEGINfieldset1END");
    }

    public function test_entries(): void
    {
        $form = new FormHandler();

        $form->borderStart();
        $form->textField("Textfield", "textfield");
        $form->borderStop();

        static::assertFormFlushContains($form, 'BEGINfieldset1Textfield:<input type="text" name="textfield" id="textfield" value="" size="20" />error_textfieldEND');
    }

    public function test_Caption(): void
    {
        $form = new FormHandler();

        $form->borderStart("thecaption");
        $form->borderStop();

        static::assertFormFlushContains($form, "BEGINfieldset1thecaptionEND");
    }

    public function test_Name(): void
    {
        $form = new FormHandler();

        $form->borderStart(null, "thename");
        $form->borderStop();

        static::assertFormFlushContains($form, "BEGINthenameEND");
    }

    public function test_Extra(): void
    {
        $form = new FormHandler();

        $form->borderStart(null, null, 'data-old="123"');
        $form->borderStop();

        static::assertFormFlushContains($form, 'BEGINfieldset1data-old="123"END');
    }
};
