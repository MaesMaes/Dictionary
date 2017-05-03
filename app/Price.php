<?php
/**
 * Created by PhpStorm.
 * User: jscheq
 * Date: 03.04.17
 * Time: 16:20
 */

namespace liw\app;


use DOMDocument;
use DOMXPath;
use SimpleXMLElement;

/**
 * Класс для нахождения цены
 *
 * Class Price
 * @package liw\app
 */
class Price
{
    public static $url = __DIR__ . '/tmp/offers.xml';
    private static $xPath;
    private static $root;
    private static $priceType = 'eff6b1b5-f3e2-11e0-aeb3-002522dad96f';

    function __construct()
    {
        $dom = new domDocument( "1.0", "utf-8" );
        $dom->load( self::$url );
        self::$root = $dom->documentElement;
        self::$xPath = new DOMXPath($dom);
    }

    static function findPriceByGoodID( $id )
    {
        $entries = self::$xPath->query('//Предложения/Предложение/Ид[.="' . $id . '"]/..//Цена[ИдТипаЦены="' . self::$priceType . '"]/ЦенаЗаЕдиницу', self::$root);
        $price = $entries->item(0)->nodeValue;

        return $price;
    }

    public function findCntByGoodID( $id )
    {
        $entries = self::$xPath->query('//Предложения/Предложение/Ид[.="' . $id . '"]/../Количество', self::$root);
        $count = $entries->item(0)->nodeValue;

        return $count;
    }

    public function findBasicUnitByGoodID( $id )
    {
        $entries = self::$xPath->query('//Предложения/Предложение/Ид[.="' . $id . '"]/../БазоваяЕдиница/@Код', self::$root);
        $code = $entries->item(0)->nodeValue;
        $entries = self::$xPath->query('//Предложения/Предложение/Ид[.="' . $id . '"]/../БазоваяЕдиница/@НаименованиеПолное', self::$root);
        $fullName = $entries->item(0)->nodeValue;
        $entries = self::$xPath->query('//Предложения/Предложение/Ид[.="' . $id . '"]/../БазоваяЕдиница/@МеждународноеСокращение', self::$root);
        $intAbbreviation = $entries->item(0)->nodeValue;
        $entries = self::$xPath->query('//Предложения/Предложение/Ид[.="' . $id . '"]/../БазоваяЕдиница', self::$root);
        $value = $entries->item(0)->nodeValue;

        return [
            'code' => $code,
            'fullName' => $fullName,
            'intAbbreviation' => $intAbbreviation,
            'value' => $value,
        ];
    }
}