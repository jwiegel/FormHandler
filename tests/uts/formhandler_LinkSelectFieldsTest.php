<?php

declare(strict_types=1);

/**
 * linkSelectFields(): the options of a select field are loaded (js/linked_select.js)
 * from a script of the project when the field before it changes
 */
final class formhandler_LinkSelectFieldsTest extends FormhandlerTestCase
{
    public function test_script(): void
    {
        $form = new FormHandler();

        $form->selectField("Country", "country", array("DE" => "Germany", "NL" => "Netherlands"));
        $form->selectField("City", "city", array());
        $form->linkSelectFields("cities.php", "country", "city");

        $html = static::assertFormFlushContains($form, [
            'FHTML/js/linked_select.js',
            "function attachcountry( aArgs, sValue ) {\n" .
                "    attachelement(\"country\", \"change\", loadcity);\n" .
                "    loadcity( aArgs, sValue );\n" .
                "}\n",
            "function loadcity( aArgs, sValue ) {\n" .
                "    var value = GetElement(\"country\").value;\n" .
                "    loadexternal(\n" .
                "      \"cities.php\",\n" .
                "      \"linkselect=true&filter=\"+encodeURIComponent(value)+\"&field=city\",\n",
            "// load the first item of the dynamic select fields\nattachcountry(",
        ]);
        static::assertStringNotContainsString('ActiveXObject', $html);
    }

    public function test_extraField(): void
    {
        $form = new FormHandler();

        $form->hiddenField("lang", "de");
        $form->selectField("Country", "country", array("DE" => "Germany"));
        $form->selectField("City", "city", array());
        $form->linkSelectFields("cities.php", array("country", "lang"), "city");

        // the value of another field is sent too, encoded
        static::assertFormFlushContains($form, [
            '&lang="+encodeURIComponent(document.forms["FormHandler"].elements["lang"].value)+"'
        ]);
    }
}
