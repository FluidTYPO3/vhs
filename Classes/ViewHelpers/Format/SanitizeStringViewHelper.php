<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * URL text segment sanitizer. Sanitizes the content into a
 * valid URL segment value which is usable in an URL without
 * further processing. For example, the text "I am Mr. Brown,
 * how are you?" becomes "i-am-mr-brown-how-are-you". Special
 * characters like diacritics or umlauts are transliterated.
 * The built-in character map can be overriden or extended by
 * providing an associative array of custom mappings.
 *
 * Also useful when creating anchor link names, for example
 * for news entries in your custom EXT:news list template, in
 * which case each news item's title would become an anchor:
 *
 * <a name="{newsItem.title -> v:format.url.sanitizeString()}"></a>
 *
 * And links would look much like the detail view links:
 *
 * /news/#this-is-a-newsitem-title
 *
 * When used with list views it has the added benefit of not
 * breaking if the item referenced is removed, it can be read
 * by Javascript (for example to dynamically expand the news
 * item being referenced). The sanitized urls are also ideal
 * to use for AJAX based detail views - and in almot all cases
 * the sanitized string will be 100% identical to the one used
 * by Realurl when translating using table lookups.
 */
class SanitizeStringViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * Basic character map
     *
     * @var array
     */
    protected static $characterMap = [
        '¹' => 1, '²' => 2, '³' => 3, '°' => 0, '€' => 'eur', 'æ' => 'ae', 'ǽ' => 'ae', 'À' => 'A',
        'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'AA', 'Ǻ' => 'A', 'Ă' => 'A', 'Ǎ' => 'A', 'Æ' => 'AE',
        'Ǽ' => 'AE', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'aa', 'ǻ' => 'a', 'ă' => 'a',
        'ǎ' => 'a', 'ª' => 'a', '@' => 'at', 'Ĉ' => 'C', 'Ċ' => 'C', 'ĉ' => 'c', 'ċ' => 'c', '©' => 'c',
        'Ð' => 'Dj', 'Đ' => 'D', 'ð' => 'dj', 'đ' => 'd', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'Ĕ' => 'E', 'Ė' => 'E', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ĕ' => 'e', 'ė' => 'e',
        'ƒ' => 'f', 'Ĝ' => 'G', 'Ġ' => 'G', 'ĝ' => 'g', 'ġ' => 'g', 'Ĥ' => 'H', 'Ħ' => 'H', 'ĥ' => 'h',
        'ħ' => 'h', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Ǐ' => 'I',
        'Į' => 'I', 'Ĳ' => 'IJ', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ĩ' => 'i', 'ĭ' => 'i',
        'ǐ' => 'i', 'į' => 'i', 'ĳ' => 'ij', 'Ĵ' => 'J', 'ĵ' => 'j', 'Ĺ' => 'L', 'Ľ' => 'L', 'Ŀ' => 'L',
        'ĺ' => 'l', 'ľ' => 'l', 'ŀ' => 'l', 'Ñ' => 'N', 'ñ' => 'n', 'ŉ' => 'n', 'Ò' => 'O', 'Ô' => 'O',
        'Õ' => 'O', 'Ō' => 'O', 'Ŏ' => 'O', 'Ǒ' => 'O', 'Ő' => 'O', 'Ơ' => 'O', 'Ø' => 'OE', 'Ǿ' => 'O',
        'Œ' => 'OE', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ō' => 'o', 'ŏ' => 'o', 'ǒ' => 'o', 'ő' => 'o',
        'ơ' => 'o', 'ø' => 'oe', 'ǿ' => 'o', 'º' => 'o', 'œ' => 'oe', 'Ŕ' => 'R', 'Ŗ' => 'R', 'ŕ' => 'r',
        'ŗ' => 'r', 'Ŝ' => 'S', 'Ș' => 'S', 'ŝ' => 's', 'ș' => 's', 'ſ' => 's', 'Ţ' => 'T', 'Ț' => 'T',
        'Ŧ' => 'T', 'Þ' => 'TH', 'ţ' => 't', 'ț' => 't', 'ŧ' => 't', 'þ' => 'th', 'Ù' => 'U', 'Ú' => 'U',
        'Û' => 'U', 'Ũ' => 'U', 'Ŭ' => 'U', 'Ű' => 'U', 'Ų' => 'U', 'Ư' => 'U', 'Ǔ' => 'U', 'Ǖ' => 'U',
        'Ǘ' => 'U', 'Ǚ' => 'U', 'Ǜ' => 'U', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ũ' => 'u', 'ŭ' => 'u',
        'ű' => 'u', 'ų' => 'u', 'ư' => 'u', 'ǔ' => 'u', 'ǖ' => 'u', 'ǘ' => 'u', 'ǚ' => 'u', 'ǜ' => 'u',
        'Ŵ' => 'W', 'ŵ' => 'w', 'Ý' => 'Y', 'Ÿ' => 'Y', 'Ŷ' => 'Y', 'ý' => 'y', 'ÿ' => 'y', 'ŷ' => 'y',
        'Ъ' => '', 'Ь' => '', 'А' => 'A', 'Б' => 'B', 'Ц' => 'C', 'Ч' => 'Ch', 'Д' => 'D', 'Е' => 'E',
        'Ё' => 'E', 'Э' => 'E', 'Ф' => 'F', 'Г' => 'G', 'Х' => 'H', 'И' => 'I', 'Й' => 'J', 'Я' => 'Ja',
        'Ю' => 'Ju', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R',
        'С' => 'S', 'Ш' => 'Sh', 'Щ' => 'Shch', 'Т' => 'T', 'У' => 'U', 'В' => 'V', 'Ы' => 'Y', 'З' => 'Z',
        'Ж' => 'Zh', 'ъ' => '', 'ь' => '', 'а' => 'a', 'б' => 'b', 'ц' => 'c', 'ч' => 'ch', 'д' => 'd',
        'е' => 'e', 'ё' => 'e', 'э' => 'e', 'ф' => 'f', 'г' => 'g', 'х' => 'h', 'и' => 'i', 'й' => 'j',
        'я' => 'ja', 'ю' => 'ju', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p',
        'р' => 'r', 'с' => 's', 'ш' => 'sh', 'щ' => 'shch', 'т' => 't', 'у' => 'u', 'в' => 'v', 'ы' => 'y',
        'з' => 'z', 'ж' => 'zh', 'Ä' => 'AE', 'Ö' => 'OE', 'Ü' => 'UE', 'ß' => 'ss', 'ä' => 'ae', 'ö' => 'oe',
        'ü' => 'ue', 'Ç' => 'C', 'Ğ' => 'G', 'İ' => 'I', 'Ş' => 'S', 'ç' => 'c', 'ğ' => 'g', 'ı' => 'i',
        'ş' => 's', 'Ā' => 'A', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'I', 'Ķ' => 'K', 'Ļ' => 'L', 'Ņ' => 'N',
        'Ū' => 'U', 'ā' => 'a', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
        'ū' => 'u', 'Ґ' => 'G', 'І' => 'I', 'Ї' => 'Ji', 'Є' => 'Ye', 'ґ' => 'g', 'і' => 'i', 'ї' => 'ji',
        'є' => 'ye', 'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T',
        'Ů' => 'U', 'Ž' => 'Z', 'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's',
        'ť' => 't', 'ů' => 'u', 'ž' => 'z', 'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'E', 'Ł' => 'L', 'Ń' => 'N',
        'Ó' => 'O', 'Ś' => 'S', 'Ź' => 'Z', 'Ż' => 'Z', 'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l',
        'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z', 'ż' => 'z', 'Α' => 'A', 'Β' => 'B', 'Γ' => 'G',
        'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'E', 'Θ' => 'Th', 'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L',
        'Μ' => 'M', 'Ν' => 'N', 'Ξ' => 'X', 'Ο' => 'O', 'Π' => 'P', 'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T',
        'Υ' => 'Y', 'Φ' => 'Ph', 'Χ' => 'Ch', 'Ψ' => 'Ps', 'Ω' => 'O', 'Ϊ' => 'I', 'Ϋ' => 'Y', 'ά' => 'a',
        'έ' => 'e', 'ή' => 'e', 'ί' => 'i', 'ΰ' => 'Y', 'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd',
        'ε' => 'e', 'ζ' => 'z', 'η' => 'e', 'θ' => 'th', 'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm',
        'ν' => 'n', 'ξ' => 'x', 'ο' => 'o', 'π' => 'p', 'ρ' => 'r', 'ς' => 's', 'σ' => 's', 'τ' => 't',
        'υ' => 'y', 'φ' => 'ph', 'χ' => 'ch', 'ψ' => 'ps', 'ω' => 'o', 'ϊ' => 'i', 'ϋ' => 'y', 'ό' => 'o',
        'ύ' => 'y', 'ώ' => 'o', 'ϐ' => 'b', 'ϑ' => 'th', 'ϒ' => 'Y', 'أ' => 'a', 'ب' => 'b', 'ت' => 't',
        'ث' => 'th', 'ج' => 'g', 'ح' => 'h', 'خ' => 'kh', 'د' => 'd', 'ذ' => 'th', 'ر' => 'r', 'ز' => 'z',
        'س' => 's', 'ش' => 'sh', 'ص' => 's', 'ض' => 'd', 'ط' => 't', 'ظ' => 'th', 'ع' => 'aa', 'غ' => 'gh',
        'ف' => 'f', 'ق' => 'k', 'ك' => 'k', 'ل' => 'l', 'م' => 'm', 'ن' => 'n', 'ه' => 'h', 'و' => 'o',
        'ي' => 'y', 'ạ' => 'a', 'ả' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
        'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
        'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ị' => 'i', 'ỉ' => 'i', 'ọ' => 'o',
        'ỏ' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ờ' => 'o', 'ớ' => 'o',
        'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ụ' => 'u', 'ủ' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u',
        'ử' => 'u', 'ữ' => 'u', 'ỳ' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'Ạ' => 'A', 'Ả' => 'A',
        'Ầ' => 'A', 'Ấ' => 'A', 'Ậ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A', 'Ằ' => 'A', 'Ắ' => 'A', 'Ặ' => 'A',
        'Ẳ' => 'A', 'Ẵ' => 'A', 'Ẹ' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E', 'Ề' => 'E', 'Ế' => 'E', 'Ệ' => 'E',
        'Ể' => 'E', 'Ễ' => 'E', 'Ị' => 'I', 'Ỉ' => 'I', 'Ọ' => 'O', 'Ỏ' => 'O', 'Ồ' => 'O', 'Ố' => 'O',
        'Ộ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ờ' => 'O', 'Ớ' => 'O', 'Ợ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O',
        'Ụ' => 'U', 'Ủ' => 'U', 'Ừ' => 'U', 'Ứ' => 'U', 'Ự' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ỳ' => 'Y',
        'Ỵ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y',
    ];

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('string', 'string', 'The string to sanitize.');
        $this->registerArgument(
            'customMap',
            'array',
            'Associative array of additional characters to replace or use to override built-in mappings.'
        );
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $string = $renderChildrenClosure();

        if (null === $string) {
            return null;
        }

        $characterMap = static::$characterMap;
        $customMap = $arguments['customMap'];
        if (true === is_array($customMap) && 0 < count($customMap)) {
            $characterMap = array_merge($characterMap, $customMap);
        }
        $specialCharsSearch = array_keys($characterMap);
        $specialCharsReplace = array_values($characterMap);
        $string = str_replace($specialCharsSearch, $specialCharsReplace, $string);
        $string = strtolower($string);
        $pattern = '/([^a-z0-9\-]){1,}/';
        $string = preg_replace($pattern, '-', $string);
        return trim($string, '-');
    }
}
