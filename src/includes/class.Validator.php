<?php

/**
 * class Validator
 *
 * Static functions to check if the given value validates a specific format
 *
 * @author Teye Heimans
 * @package FormHandler
 */
class Validator
{
    /**
     * Validator::IsString()
     *
     * Any string that doesn't have control characters (ASCII 0 - 31) but spaces are allowed
     *
     * @param string $value: The string to check
     * @return bool
     */
    public static function IsString($value)
    {
        return preg_match("/^[^\x-\x1F]+$/", $value);
    }

    /**
     * Validator::_IsString()
     *
     * Public: same as IsString, only now the value is also valid if it is empty
     *
     * @param string $value
     * @return bool
     */
    public static function _IsString($value)
    {
        return StrLen($value) == 0 || Validator::IsString($value);
    }

    /**
     * Validator::IsAlpha()
     *
     * Public: only letters a-z and A-Z
     *
     * @param string $value
     * @return bool
     */
    public static function IsAlpha($value)
    {
        return (bool)preg_match("/^[a-z]+$/i", $value);
    }

    /**
     * Validator::_IsAlpha()
     *
     * Public: same as IsAlpha, only now the value is also valid if it is empty
     *
     * @param string $value
     * @return bool
     */
    public static function _IsAlpha($value)
    {
        return StrLen($value) == 0 || Validator::IsAlpha($value);
    }

    /**
     * Validator::IsDigit()
     *
     * Public: only numbers 0-9
     *
     * @param string $value
     * @return bool
     */
    public static function IsDigit($value)
    {
        return (bool) preg_match("/^[0-9]+$/", $value);
    }

    /**
     * Validator::_IsDigit()
     *
     * Public: same as IsDigit, only now the value is also valid if it is empty
     *
     * @param string $value
     * @return bool
     */
    public static function _IsDigit($value)
    {
        return StrLen($value) == 0 || Validator::IsDigit($value);
    }

    /**
     * Validator::IsAlphaNum()
     *
     * Public: letters and numbers
     *
     * @param string $value
     * @return bool
     */
    public static function IsAlphaNum($value)
    {
        return (bool)preg_match("/^[a-z0-9]+$/i", $value);
    }

    /**
     * Validator::_IsAlphaNum()
     *
     * Public: same as IsAlphaNum, only now the value is also valid if it is empty
     *
     * @param string $value
     * @return bool
     */
    public static function _IsAlphaNum($value)
    {
        return StrLen($value) == 0 || Validator::IsAlphaNum($value);
    }

    /**
     * Validator::IsFloat()
     *
     * Public: only numbers 0-9 and an optional - (minus) sign (in the beginning only)
     *
     * @param string $value
     * @return bool
     */
    public static function IsFloat($value)
    {
        return (bool) preg_match("/^-?([0-9]*\.?,?[0-9]+)$/", $value);
    }

    /**
     * Validator::_IsFloat()
     *
     * Public: same as IsFloat, only now the value is also valid if it is empty
     *
     * @param string $value
     * @return bool
     */
    public static function _IsFloat($value)
    {
        return StrLen($value) == 0 || Validator::IsFloat($value);
    }

    /**
     * Validator::IsInteger()
     *
     * Public: only numbers 0-9 and an optional - (minus) sign (in the beginning only)
     *
     * @param string $value
     * @return bool
     */
    public static function IsInteger($value)
    {
        return (bool) preg_match("/^-?[0-9]+$/", $value);
    }

    /**
     * Validator::_IsInteger()
     *
     * Public: same as IsInteger, only now the value is also valid if it is empty
     *
     * @param string $value
     * @return bool
     */
    public static function _IsInteger($value)
    {
        return StrLen($value) == 0 || Validator::IsInteger($value);
    }

    /**
     * Validator::IsFilename()
     *
     * Public: a valid file name (including dots but no slashes and other forbidden characters)
     *
     * @param string $value
     * @return bool
     */
    public static function IsFilename($value)
    {
        return preg_match("{^[^\\/\*\?\:\,]+$}", $value);
    }

    /**
     * Validator::_IsFilename()
     *
     * Public: same as IsFilename, only now the value is also valid if it is empty
     *
     * @param string $value
     * @return bool
     */
    public static function _IsFilename($value)
    {
        return StrLen($value) == 0 || Validator::IsFilename($value);
    }

    /**
     * Validator::IsBool()
     *
     * Public: a boolean (case-insensitive "true"/"1" or "false"/"0")
     *
     * @param string|bool $value
     * @return bool
     */
    public static function IsBool(&$value)
    {
        if (preg_match('/^(true|1|false|0)$/i', (string) $value))
        {
            $value = in_array(strtolower((string) $value), array('true', '1'), true);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Validator::_IsBool()
     *
     * Public: same as IsBool, only now the value is also valid if it is empty
     *
     * @param string $value
     * @return bool
     */
    public static function _IsBool($value)
    {
        return StrLen($value) == 0 || Validator::IsBool($value);
    }

    // a valid variable name (letters, digits, underscore)
    public static function IsVariabele($value)
    {
        if ($value == '_')
        {
            return false;
        }
        else if (preg_match("/^[a-zA-Z_][a-zA-Z0-9_]*$/i", $value))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function _IsVariabele($value)
    {
        return StrLen($value) == 0 || Validator::IsVariabele($value);
    }

    // a valid password (alphanumberic + some other characters but no spaces. Only allow ASCII 33 - 126)
    public static function IsPassword($value)
    {
        return preg_match("/^[\41-\176]+$/", $value);
    }

    public static function _IsPassword($value)
    {
        return StrLen($value) == 0 || Validator::IsPassword($value);
    }

    // check for a valid url
    public static function IsURL($value)
    {
        $regex =
            '/^((https?|ftp):\/\/)?' .                                         // optional scheme
            '([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,63}' . // host name
            '(:[0-9]{1,5})?' .                                                 // optional port
            '([\/?#][^\s]*)?$/';                                               // optional path, query, fragment

        return preg_match($regex, $value);
    }

    public static function _IsURL($value)
    {
        return StrLen($value) == 0 || Validator::IsURL($value);
    }

    // a valid URL (http connection is used to check if url exists!)
    public static function IsURLHost($href)
    {
        if (strlen($href) <= 3)
        {
            return false;
        }

        if (!preg_match("/^[a-z]+:/i", $href))
        {
            $href = 'http://' . $href;
        }
        if (preg_match("/^https?:\/\//", $href))
        {
            $fp = @fopen($href, 'r');
            if ($fp)
            {
                @fclose($fp);

                return true;
            }
        }
        return false;
    }

    public static function _IsURLHost($value)
    {
        return StrLen($value) == 0 || Validator::IsURLHost($value);
    }

    // a valid email address (only checks for valid format: xxx@xxx.xxx)
    public static function IsEmail($value)
    {
        return preg_match("/^[a-z0-9_\.-]+@([a-z0-9]+([\-]+[a-z0-9]+)*\.)+[a-z]{2,7}$/i", $value);
    }

    public static function _IsEmail($value)
    {
        return StrLen($value) == 0 || Validator::IsEmail($value);
    }

    // like IsMail only with host check
    public static function IsEmailHost($value)
    {
        $check = array();
        if (preg_match("/^[0-9A-Za-z_]([-_.]?[0-9A-Za-z_])*@[0-9A-Za-z][-.0-9A-Za-z]*\\.[a-zA-Z]{2,3}[.]?$/", $value, $check))
        {
            $host = substr(strstr($check[0], '@'), 1) . ".";

            if (function_exists('getmxrr'))
            {
                $tmp = null;
                if (getmxrr($host, $tmp))
                    return true;
                // this will catch dns that are not mx.
                if (checkdnsrr($host, 'ANY'))
                    return true;
            }
            else
            {
                return ($host != gethostbyname($host));
            }
        }

        return false;
    }

    public static function _IsEmailHost($value)
    {
        return StrLen($value) == 0 || Validator::IsEmailHost($value);
    }

    // like IsString, but newline characters and tabs are allowed
    public static function IsText($value)
    {
        return
            preg_match("/^([^\x-\x1F]|[\r\n\t])+$/", $value);
    }

    public static function _IsText($value)
    {
        return StrLen($value) == 0 || Validator::IsText($value);
    }

    // is a valid dutch postcode (eg. 9999 AA)
    public static function IsPostcode($value)
    {
        return preg_match('/^[1-9][0-9]{3} ?[a-zA-Z]{2}$/', $value);
    }

    public static function _IsPostcode($value)
    {
        return StrLen($value) == 0 || Validator::IsPostcode($value);
    }

    // is a valid dutch phone-number
    public static function IsPhone($value)
    {
        $regex = '/^[0-9]{2,4}[-]?[0-9]{6,8}$/';
        $value = str_replace(array(' ', '-'), array('', ''), $value);
        return (strLen($value) == 10 && preg_match($regex, $value));
    }

    public static function _IsPhone($value)
    {
        return StrLen($value) == 0 || Validator::IsPhone($value);
    }

    // check if the value is not empty
    public static function notEmpty($value)
    {
        if (!is_array($value))
        {
            $value = trim($value);
            if ($value != '')
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return (bool) (count($value) > 0);
        }
    }

    // check if it's a valid ip adres
    public static function IsIp($ip)
    {
        // four numbers from 0 to 255, optional port
        if (!preg_match('/^([0-9.]+)(:[0-9]{1,5})?$/', $ip, $match))
        {
            return false;
        }
        return filter_var($match[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    public static function _IsIp($ip)
    {
        return StrLen($ip) == 0 || Validator::IsIp($ip);
    }

    // check if the value does not contains any html
    public static function NoHTML($value)
    {
        return strip_tags($value) == $value && strlen($value) > 0;
    }

    public static function _NoHTML($value)
    {
        return StrLen($value) == 0 || Validator::noHTML($value);
    }

    /**
     * Check the capthcafield using Securimage
     *
     * @param string $value
     * @return boolean
     * @author Johan Wiegel
     * @since 27-11-2008
     */
    public static function FH_CAPTCHA($value)
    {
        require(FH_FHTML_INCLUDE_DIR . 'securimage/securimage.php');
        $img = new Securimage(); // @phpstan-ignore-line (Instantiated class Securimage not found.)
        $valid = $img->check($value); // @phpstan-ignore-line (Call to method check() on an unknown class Securimage.)
        if ($valid == true)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
