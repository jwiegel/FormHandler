<?php

declare(strict_types=1);

final class formhandler_LookAndFeel_newPageTest extends FormhandlerTestCase
{
    public function test(): void
    {
        $form = new FormHandler();

        //first page... 
        $form->textField("Question 1", "q1", FH_STRING, 30, 50);
        $form->submitButton("Next page");

        // second page 
        $form->newPage();
        $form->textArea("Question 2", "q2", FH_TEXT);
        $form->submitButton("Next Page");

        // third and last page 
        $form->newPage();
        $form->textField("Question 3", "q3", FH_STRING);
        $form->submitButton("Submit");

        static::assertFormFlushContains($form, [
            '<input type="hidden" name="q2" id="q2" value="" />',
            '<input type="hidden" name="q3" id="q3" value="" />',
            '<input type="hidden" name="FormHandler_page" id="FormHandler_page" value="1" />',
            'Question 1:<input type="text" name="q1" id="q1" value="" size="30" maxlength="50" />error_q1',
            '<input type="submit" value="Next page" name="button1" id="button1"  onclick=" if (this.form.querySelector(\':invalid\') == null) { this.form.submit();this.disabled=true;}"  />error_button1'
        ]);
    }

    public function test_page2(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['q1'] = "anwser1";
        $_POST['q2'] = "";
        $_POST['q3'] = "";
        $_POST['FormHandler_page'] = "1";

        $form = new FormHandler();

        //first page... 
        $form->textField("Question 1", "q1", FH_STRING, 30, 50);
        $form->submitButton("Next page");

        // second page 
        $form->newPage();
        $form->textArea("Question 2", "q2", FH_TEXT);
        $form->submitButton("Next Page");

        // third and last page 
        $form->newPage();
        $form->textField("Question 3", "q3", FH_STRING);
        $form->submitButton("Submit");

        static::assertFormFlushContains($form, [
            '<input type="hidden" name="q1" id="q1" value="anwser1" />',
            '<input type="hidden" name="q3" id="q3" value="" />',
            '<input type="hidden" name="FormHandler_page" id="FormHandler_page" value="2" />',
            'Question 2:<textarea name="q2" id="q2" cols="40" rows="7"></textarea>error_q2',
            '<input type="submit" value="Next Page" name="button2" id="button2"  onclick=" if (this.form.querySelector(\':invalid\') == null) { this.form.submit();this.disabled=true;}"  />error_button2'
        ]);
    }

    public function test_page3(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['q1'] = "anwser1";
        $_POST['q2'] = "answer2";
        $_POST['q3'] = "";
        $_POST['FormHandler_page'] = "2";

        $form = new FormHandler();

        //first page... 
        $form->textField("Question 1", "q1", FH_STRING, 30, 50);
        $form->submitButton("Next page");

        // second page 
        $form->newPage();
        $form->textArea("Question 2", "q2", FH_TEXT);
        $form->submitButton("Next Page");

        // third and last page 
        $form->newPage();
        $form->textField("Question 3", "q3", FH_STRING);
        $form->submitButton("Submit");

        static::assertFormFlushContains($form, [
            '<input type="hidden" name="q1" id="q1" value="anwser1" />',
            '<input type="hidden" name="q2" id="q2" value="answer2" />',
            '<input type="hidden" name="FormHandler_page" id="FormHandler_page" value="3" />',
            'Question 3:<input type="text" name="q3" id="q3" value="" size="20" />error_q3',
            '<input type="submit" value="Submit" name="button3" id="button3"  onclick=" if (this.form.querySelector(\':invalid\') == null) { this.form.submit();this.disabled=true;}"  />error_button3'
        ]);
    }
};
