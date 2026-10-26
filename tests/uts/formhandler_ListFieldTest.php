<?php

declare(strict_types=1);

// for fewer text in unittests
define('FH_LISTFIELD_HORIZONTAL_MASK', "%onlabel%%offlabel%%onfield%%name%%ontitle%%offfield%%offtitle%");
define('FH_LISTFIELD_VERTICAL_MASK', "%offlabel%%offfield%%name%%offtitle%%ontitle%%onlabel%%onfield%");


final class formhandler_ListFieldTest extends FormhandlerTestCase
{
    private $aElements = [
        1 => "elem1",
        2 => "elem2",
        3 => "elem3",
        4 => "elem4"
    ];

    public function test_new_horizontal(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->listField("Listfield", "listfield", $this->aElements);

        static::assertEmpty($form->getValue("listfield"));

        static::assertFormFlushContains($form, [
            'Listfield:<input type="hidden" name="listfield" id="listfield" value="" />',
            'SelectedAvailable',
            '<select name="listfield_ListOn[]" id="listfield_ListOn" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', false)">',
            '<option>&nbsp;</option>',
            '</select>listfieldSelect an item to move to the Available box or double click to move all items',
            '<select name="listfield_ListOff[]" id="listfield_ListOff" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', true)">',
            '<option  value="1" >elem1</option>',
            '<option  value="2" >elem2</option>',
            '<option  value="3" >elem3</option>',
            '<option  value="4" >elem4</option>',
            '</select>Select an item to move to the Selected box or double click to move all itemserror_listfield'
        ]);
    }

    public function test_new_vertical(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->listField("Listfield", "listfield", $this->aElements, null, null, null, null, null, null, true);

        static::assertEmpty($form->getValue("listfield"));

        static::assertFormFlushContains($form, [
            'Listfield:<input type="hidden" name="listfield" id="listfield" value="" />',
            'Available',
            '<select name="listfield_ListOff[]" id="listfield_ListOff" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', true)">',
            '<option  value="1" >elem1</option>',
            '<option  value="2" >elem2</option>',
            '<option  value="3" >elem3</option>',
            '<option  value="4" >elem4</option>',
            '</select>listfieldSelect an item to move to the Selected box or double click to move all itemsSelect an item to move to the Available box or double click to move all items',
            'Selected<select name="listfield_ListOn[]" id="listfield_ListOn" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', false)">',
            '<option>&nbsp;</option>',
            '</select>error_listfield'
        ]);
    }

    public function test_posted(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['listfield'] = ['2', '3'];

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->listField("Listfield", "listfield", $this->aElements);

        static::assertEquals(['2', '3'], $form->getValue("listfield"));
    }

    public function test_posted_empty(): void
    {
        $_POST['FormHandler_submit'] = "1";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->listField("Listfield", "listfield", $this->aElements);

        static::assertEquals([], $form->getValue("listfield"));
    }

    public function test_posted_useArrayKeyAsValueFalse(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['listfield'] = ['elem2', 'elem3'];

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->listField("Listfield", "listfield", $this->aElements, null, false);

        static::assertEquals(['elem2', 'elem3'], $form->getValue("listfield"));
    }

    public function test_posted_fillvalue_byinvalid_horizontal(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['listfield'] = ['2', '3'];

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->listField("Listfield", "listfield", $this->aElements);

        static::assertEquals(['2', '3'], $form->getValue("listfield"));

        $form->setError("listfield", "forcedError");

        static::assertFormFlushContains($form, [
            'Listfield:<input type="hidden" name="listfield" id="listfield" value="2,3" />',
            'SelectedAvailable',
            '<select class="error" name="listfield_ListOn[]" id="listfield_ListOn" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', false)">',
            '<option  value="2" >elem2</option>',
            '<option  value="3" >elem3</option>',
            '</select>listfieldSelect an item to move to the Available box or double click to move all items',
            '<select class="error" name="listfield_ListOff[]" id="listfield_ListOff" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', true)">',
            '<option  value="1" >elem1</option>',
            '<option  value="4" >elem4</option>',
            '</select>Select an item to move to the Selected box or double click to move all itemserror_listfield',
            '<span id="error_listfield" class="error">forcedError</span>'
        ]);
    }

    public function test_posted_fillvalue_byinvalid_vertical(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['listfield'] = ['2', '3'];

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->listField("Listfield", "listfield", $this->aElements, null, null, null, null, null, null, true);

        static::assertEquals(['2', '3'], $form->getValue("listfield"));

        $form->setError("listfield", "forcedError");

        static::assertFormFlushContains($form, [
            'Listfield:<input type="hidden" name="listfield" id="listfield" value="2,3" />',
            'Available',
            '<select class="error" name="listfield_ListOff[]" id="listfield_ListOff" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', true)">',
            '<option  value="1" >elem1</option>',
            '<option  value="4" >elem4</option>',
            '</select>listfieldSelect an item to move to the Selected box or double click to move all itemsSelect an item to move to the Available box or double click to move all items',
            'Selected<select class="error" name="listfield_ListOn[]" id="listfield_ListOn" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', false)">',
            '<option  value="2" >elem2</option>',
            '<option  value="3" >elem3</option>',
            '</select>error_listfield',
            '<span id="error_listfield" class="error">forcedError</span>'
        ]);
    }

    public function test_validator(): void
    {
        $_POST['FormHandler_submit'] = "1";

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->listField("Listfield", "listfield", $this->aElements, FH_NOT_EMPTY);

        static::assertEmpty($form->getValue("listfield"));

        $t = $form->catchErrors(false);

        static::assertEquals(
            '<span id="error_listfield" class="error">You did not enter a correct value for this field!</span>',
            $t['listfield']
        );
    }

    public function test_new_horizontal_useArrayKeyAsValueFalse(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->listField("Listfield", "listfield", $this->aElements, null, false);

        static::assertEmpty($form->getValue("listfield"));

        static::assertFormFlushContains($form, [
            'Listfield:<input type="hidden" name="listfield" id="listfield" value="" />',
            'SelectedAvailable',
            '<select name="listfield_ListOn[]" id="listfield_ListOn" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', false)">',
            '<option>&nbsp;</option>',
            '</select>listfieldSelect an item to move to the Available box or double click to move all items',
            '<select name="listfield_ListOff[]" id="listfield_ListOff" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', true)">',
            '<option  value="elem1" >elem1</option>',
            '<option  value="elem2" >elem2</option>',
            '<option  value="elem3" >elem3</option>',
            '<option  value="elem4" >elem4</option>',
            '</select>Select an item to move to the Selected box or double click to move all itemserror_listfield'
        ]);
    }

    public function test_new_vertical_useArrayKeyAsValueFalse(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->listField("Listfield", "listfield", $this->aElements, null, false, null, null, null, null, true);

        static::assertEmpty($form->getValue("listfield"));

        static::assertFormFlushContains($form, [
            'Listfield:<input type="hidden" name="listfield" id="listfield" value="" />',
            'Available',
            '<select name="listfield_ListOff[]" id="listfield_ListOff" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', true)">',
            '<option  value="elem1" >elem1</option>',
            '<option  value="elem2" >elem2</option>',
            '<option  value="elem3" >elem3</option>',
            '<option  value="elem4" >elem4</option>',
            '</select>listfieldSelect an item to move to the Selected box or double click to move all itemsSelect an item to move to the Available box or double click to move all items',
            'Selected<select name="listfield_ListOn[]" id="listfield_ListOn" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', false)">',
            '<option>&nbsp;</option>',
            '</select>error_listfield'
        ]);
    }

    public function test_posted_fillvalue_byinvalid_horizontal_useArrayKeyAsValueFalse(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['listfield'] = ['elem2', 'elem3'];

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->listField("Listfield", "listfield", $this->aElements, null, false);

        static::assertEquals(['elem2', 'elem3'], $form->getValue("listfield"));

        $form->setError("listfield", "forcedError");

        static::assertFormFlushContains($form, [
            'Listfield:<input type="hidden" name="listfield" id="listfield" value="elem2,elem3" />',
            'SelectedAvailable',
            '<select class="error" name="listfield_ListOn[]" id="listfield_ListOn" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', false)">',
            '<option  value="elem2" >elem2</option>',
            '<option  value="elem3" >elem3</option>',
            '</select>listfieldSelect an item to move to the Available box or double click to move all items',
            '<select class="error" name="listfield_ListOff[]" id="listfield_ListOff" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', true)">',
            '<option  value="elem1" >elem1</option>',
            '<option  value="elem4" >elem4</option>',
            '</select>Select an item to move to the Selected box or double click to move all itemserror_listfield',
            '<span id="error_listfield" class="error">forcedError</span>'
        ]);
    }

    public function test_posted_fillvalue_byinvalid_vertical_useArrayKeyAsValueFalse(): void
    {
        $_POST['FormHandler_submit'] = "1";
        $_POST['listfield'] = ['elem2', 'elem3'];

        $form = new FormHandler();

        static::assertTrue($form->isPosted());

        $form->listField("Listfield", "listfield", $this->aElements, null, false, null, null, null, null, true);

        static::assertEquals(['elem2', 'elem3'], $form->getValue("listfield"));

        $form->setError("listfield", "forcedError");

        static::assertFormFlushContains($form, [
            'Listfield:<input type="hidden" name="listfield" id="listfield" value="elem2,elem3" />',
            'Available',
            '<select class="error" name="listfield_ListOff[]" id="listfield_ListOff" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', true)">',
            '<option  value="elem1" >elem1</option>',
            '<option  value="elem4" >elem4</option>',
            '</select>listfieldSelect an item to move to the Selected box or double click to move all itemsSelect an item to move to the Available box or double click to move all items',
            'Selected<select class="error" name="listfield_ListOn[]" id="listfield_ListOn" size="4" multiple="multiple"  ondblclick="changeValue(\'listfield\', false)">',
            '<option  value="elem2" >elem2</option>',
            '<option  value="elem3" >elem3</option>',
            '</select>error_listfield',
            '<span id="error_listfield" class="error">forcedError</span>'
        ]);
    }

    public function test_new_offtitle_ontitle(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->listField("ListfieldV", "listfieldV", $this->aElements, null, null, "OnTitleV", "OffTitleV");
        $form->listField("ListfieldH", "listfieldH", $this->aElements, null, null, "OnTitleH", "OffTitleH", null, null, false);

        static::assertFormFlushContains($form, [
            'Select an item to move to the OffTitleV box or double click to move all items',
            'Select an item to move to the OnTitleV box or double click to move all',
            'Select an item to move to the OffTitleH box or double click to move all items',
            'Select an item to move to the OnTitleH box or double click to move all items'
        ]);
    }

    public function test_new_size(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->listField("ListfieldV", "listfieldV", $this->aElements, null, null, null, null, 123);
        $form->listField("ListfieldH", "listfieldH", $this->aElements, null, null, null, null, 123, null, false);

        static::assertFormFlushContains($form, [
            'id="listfieldV_ListOn" size="123"',
            'id="listfieldV_ListOff" size="123"',
            'id="listfieldH_ListOn" size="123"',
            'id="listfieldH_ListOff" size="123"'
        ]);
    }

    public function test_new_extra(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->listField("ListfieldV", "listfieldV", $this->aElements, null, null, null, null, null, 'data-old="123"');
        $form->listField("ListfieldH", "listfieldH", $this->aElements, null, null, null, null, null, 'data-old="123"', false);

        static::assertFormFlushContains($form, [
            'id="listfieldV_ListOn" size="4" multiple="multiple" data-old="123"',
            'id="listfieldV_ListOff" size="4" multiple="multiple" data-old="123"',
            'id="listfieldH_ListOn" size="4" multiple="multiple" data-old="123"',
            'id="listfieldH_ListOff" size="4" multiple="multiple" data-old="123"'
        ]);
    }
};
