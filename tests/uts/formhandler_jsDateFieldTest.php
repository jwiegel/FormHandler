<?php

declare(strict_types=1);

final class formhandler_jsDateFieldTest extends FormhandlerTestCase
{
    public function test_new(): void
    {
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->jsDateField("Datefield", "datefield");

        $year = date("Y");
        $disableDate1 = $year - 91;
        $disableDate2 = $year + 1;

        static::assertFormFlushContains($form, [
            'FHTML/js/calendar_popup.js',
            'document.write(getCalendarStyles());',
            'function getDateString( fldForm, fldYear, fldMonth, fldDay ) {',
            "</select> <a href='javascript:;' onclick=\"if( cal_datefield ) cal_datefield.showCalendar('anchor_datefield', getDateString('FormHandler','datefield_year', 'datefield_month', 'datefield_day')); return false;\"  name='anchor_datefield' id='anchor_datefield'><img src=",
            "FHTML/images/calendar.gif' border='0' alt='Select Date' /></a>\n" .
                "<span id='datefield_span'  style='position:absolute;visibility:hidden;background-color:white;layer-background-color:white;'></span>\n" .
                "error_datefield",
            "if( document.getElementById('datefield_span') ) \n" .
                "{\n" .
                "   var cal_datefield = new CalendarPopup('datefield_span');\n" .
                "   cal_datefield.setMonthNames('January','February','March','April','May','June','July','August','September','October','November','December');\n" .
                "   cal_datefield.setDayHeaders('S','M','T','W','T','F','S');\n" .
                "   cal_datefield.setWeekStartDay(1);\n" .
                "   cal_datefield.setTodayText('Today');\n" .
                "   cal_datefield.showYearNavigation();\n" .
                "   cal_datefield.showYearNavigationInput();\n" .
                "   cal_datefield.setReturnFunction('setdatefieldValues');\n" .
                "   cal_datefield.addDisabledDates(null,'Dec 31, {$disableDate1}');\n" .
                "   cal_datefield.addDisabledDates('Jan 1, {$disableDate2}',null);\n" .
                "   function setdatefieldValues(y,m,d) {\n" .
                "       document.forms['FormHandler'].elements['datefield_day'].value   = LZ(d);\n" .
                "       document.forms['FormHandler'].elements['datefield_month'].value = LZ(m);\n" .
                "       document.forms['FormHandler'].elements['datefield_year'].value  = y;\n" .
                "   }\n" .
                "}\n",
        ]);
    }

    public function test_useDropdown(): void
    {
        define('FH_JSCALENDARPOPUP_USE_DROPDOWN', true);
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->jsDateField("Datefield", "datefield");

        $year = date("Y");
        $disableDate1 = $year - 91;
        $disableDate2 = $year + 1;

        static::assertFormFlushContains($form, [
            'FHTML/js/calendar_popup.js',
            'document.write(getCalendarStyles());',
            'function getDateString( fldForm, fldYear, fldMonth, fldDay ) {',
            "</select> <a href='javascript:;' onclick=\"if( cal_datefield ) cal_datefield.showCalendar('anchor_datefield', getDateString('FormHandler','datefield_year', 'datefield_month', 'datefield_day')); return false;\"  name='anchor_datefield' id='anchor_datefield'><img src=",
            "FHTML/images/calendar.gif' border='0' alt='Select Date' /></a>\n" .
                "<span id='datefield_span'  style='position:absolute;visibility:hidden;background-color:white;layer-background-color:white;'></span>\n" .
                "error_datefield",
            "if( document.getElementById('datefield_span') ) \n" .
                "{\n" .
                "   var cal_datefield = new CalendarPopup('datefield_span');\n" .
                "   cal_datefield.setMonthNames('January','February','March','April','May','June','July','August','September','October','November','December');\n" .
                "   cal_datefield.setDayHeaders('S','M','T','W','T','F','S');\n" .
                "   cal_datefield.setWeekStartDay(1);\n" .
                "   cal_datefield.setTodayText('Today');\n" .
                "   cal_datefield.showYearNavigation();\n" .
                "   cal_datefield.showYearNavigationInput();\n" .
                "   cal_datefield.showNavigationDropdowns();\n" .
                "   cal_datefield.setReturnFunction('setdatefieldValues');\n" .
                "   cal_datefield.addDisabledDates(null,'Dec 31, {$disableDate1}');\n" .
                "   cal_datefield.addDisabledDates('Jan 1, {$disableDate2}',null);\n" .
                "   function setdatefieldValues(y,m,d) {\n" .
                "       document.forms['FormHandler'].elements['datefield_day'].value   = LZ(d);\n" .
                "       document.forms['FormHandler'].elements['datefield_month'].value = LZ(m);\n" .
                "       document.forms['FormHandler'].elements['datefield_year'].value  = y;\n" .
                "   }\n" .
                "}\n",
        ]);
    }

    public function test_startday(): void
    {
        define('FH_JSCALENDARPOPUP_STARTDAY', 0);
        $form = new FormHandler();

        static::assertFalse($form->isPosted());

        $form->jsDateField("Datefield", "datefield");

        $year = date("Y");
        $disableDate1 = $year - 91;
        $disableDate2 = $year + 1;

        static::assertFormFlushContains($form, [
            'FHTML/js/calendar_popup.js',
            'document.write(getCalendarStyles());',
            'function getDateString( fldForm, fldYear, fldMonth, fldDay ) {',
            "</select> <a href='javascript:;' onclick=\"if( cal_datefield ) cal_datefield.showCalendar('anchor_datefield', getDateString('FormHandler','datefield_year', 'datefield_month', 'datefield_day')); return false;\"  name='anchor_datefield' id='anchor_datefield'><img src=",
            "FHTML/images/calendar.gif' border='0' alt='Select Date' /></a>\n" .
                "<span id='datefield_span'  style='position:absolute;visibility:hidden;background-color:white;layer-background-color:white;'></span>\n" .
                "error_datefield",
            "if( document.getElementById('datefield_span') ) \n" .
                "{\n" .
                "   var cal_datefield = new CalendarPopup('datefield_span');\n" .
                "   cal_datefield.setMonthNames('January','February','March','April','May','June','July','August','September','October','November','December');\n" .
                "   cal_datefield.setDayHeaders('S','M','T','W','T','F','S');\n" .
                "   cal_datefield.setWeekStartDay(0);\n" .
                "   cal_datefield.setTodayText('Today');\n" .
                "   cal_datefield.showYearNavigation();\n" .
                "   cal_datefield.showYearNavigationInput();\n" .
                "   cal_datefield.setReturnFunction('setdatefieldValues');\n" .
                "   cal_datefield.addDisabledDates(null,'Dec 31, {$disableDate1}');\n" .
                "   cal_datefield.addDisabledDates('Jan 1, {$disableDate2}',null);\n" .
                "   function setdatefieldValues(y,m,d) {\n" .
                "       document.forms['FormHandler'].elements['datefield_day'].value   = LZ(d);\n" .
                "       document.forms['FormHandler'].elements['datefield_month'].value = LZ(m);\n" .
                "       document.forms['FormHandler'].elements['datefield_year'].value  = y;\n" .
                "   }\n" .
                "}\n",
        ]);
    }
};
