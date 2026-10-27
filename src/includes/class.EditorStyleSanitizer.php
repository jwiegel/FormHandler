<?php

declare(strict_types=1);

use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface;

/**
 * class EditorStyleSanitizer
 *
 * Checks the style attribute of the editor's HTML when it is allowed
 * (Editor::isStyleAllowed()). The editor only writes simple declarations like
 * color, font-size or text-align; a style with resources or script
 * constructs is removed completely.
 *
 * @package FormHandler
 */
class EditorStyleSanitizer implements AttributeSanitizerInterface
{
    private const FORBIDDEN = '/url\s*\(|expression\s*\(|javascript\s*:|@import|behavior\s*:|-moz-binding|\\\\/i';

    public function getSupportedElements(): ?array
    {
        return null;
    }

    public function getSupportedAttributes(): ?array
    {
        return array('style');
    }

    public function sanitizeAttribute(string $element, string $attribute, string $value, HtmlSanitizerConfig $config): ?string
    {
        return preg_match(self::FORBIDDEN, $value) ? null : $value;
    }
}
