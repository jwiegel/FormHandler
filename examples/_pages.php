<?php

/**
 * All example pages: file => array(title, manual page)
 * The manual pages are at https://www.fh3.nl/manual/<manual page>
 */
return array(
    'Fields' => array(
        'textfield.php'       => array('TextField', '23/TextField.html'),
        'passfield.php'       => array('PassField', '24/PassField.html'),
        'hiddenfield.php'     => array('HiddenField', '25/HiddenField.html'),
        'textarea.php'        => array('TextArea', '26/TextArea.html'),
        'selectfield.php'     => array('SelectField', '27/SelectField.html'),
        'checkbox.php'        => array('CheckBox', '28/CheckBox.html'),
        'radiobutton.php'     => array('RadioButton', '29/RadioButton.html'),
        'uploadfield.php'     => array('UploadField', '30/UploadField.html'),
        'listfield.php'       => array('ListField', '31/ListField.html'),
        'editor.php'          => array('Editor', '112/Editor.html'),
        'datefield.php'       => array('DateField', '33/DateField.html'),
        'jsdatefield.php'     => array('jsDateField', '34/jsDateField.html'),
        'datetextfield.php'   => array('DateTextField', '110/DateTextField.html'),
        'jsdatetextfield.php' => array('jsDateTextField', '111/jsDateTextField.html'),
        'html5datefield.php'  => array('html5DateField', null), // new in 4.2, not in the manual
        'timefield.php'       => array('TimeField', '35/TimeField.html'),
        'browserfield.php'    => array('BrowserField', '97/BrowserField.html'),
        'colorpicker.php'     => array('ColorPicker', '100/ColorPicker.html'),
        'textselectfield.php' => array('TextSelectField', '101/TextSelectField.html'),
        'captchafield.php'    => array('CaptchaField', '103/CaptchaField.html'),
    ),
    'Buttons' => array(
        'buttons.php'         => array('All buttons', '11/Buttons.html'),
    ),
    'Look & Feel' => array(
        'helptext.php'        => array('SetHelpText, SetMaxLength', '43/SetHelpText.html'),
        'border.php'          => array('BorderStart, AddLine, AddHTML', '47/BorderStart.html'),
        'mask.php'            => array('SetMask, UseTable', '50/SetMask.html'),
        'autocomplete.php'    => array('SetAutoComplete', '52/SetAutoComplete.html'),
        'newpage.php'         => array('NewPage', '53/NewPage_%2A.html'),
        'viewmode.php'        => array('EnableViewMode, SetFieldViewMode', '58/EnableViewMode.html'),
        'language.php'        => array('SetLanguage, SetFocus, SetTabIndex', '55/SetLanguage.html'),
    ),
    'Data handling' => array(
        'data.php'            => array('SetValue, AddValue, OnCorrect, SetError', '13/Data_handling.html'),
    ),
    'Database' => array(
        'db_form.php'         => array('dbFormHandler: insert and edit', '88/dbFormHandler.html'),
        'db_fields.php'       => array('dbSelectField, dbListField, dbCheckBox, dbRadioButton, dbTextSelectField', '14/Database_Functions.html'),
        'db_junction.php'     => array('addJunctionTable: n:m table', '109/addJunctionTable.html'),
    ),
);
