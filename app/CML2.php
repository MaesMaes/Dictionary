<?php
/**
 * Created by PhpStorm.
 * User: jscheq
 * Date: 24.03.17
 * Time: 13:42
 */

namespace liw\app;

use DOMDocument;
use DOMXPath;
use MongoDB\Driver\Exception\Exception;
use SimpleXMLElement;

/**
 * Класс для получения данных из import.xml
 *
 * Class CML2
 * @package liw\app
 */
class CML2
{
    private static $cml2_path = __DIR__ . '/tmp/import.xml';
    private static $classifier;
    private static $goods;
    private static $obj;

    private static $xPath;
    private static $root;

    public function __construct()
    {
        self::$obj = self::getCML2Object();
        self::$classifier = self::$obj->{'Классификатор'}->{'Группы'};
        self::$goods = self::$obj->{'Каталог'}->{'Товары'};

        // Создаём XML-документ версии 1.0 с кодировкой utf-8
        $dom = new domDocument( "1.0", "utf-8" );

        // Загружаем XML-документ из файла в объект DOM
        $dom->load( self::$cml2_path );

        // Получаем корневой элемент
        self::$root = $dom->documentElement;
        self::$xPath = new DOMXPath($dom);
    }

    public static function findGroupByIDClassifier( $id )
    {
        $info = [];

        try
        {
            $entries = self::$xPath->query("//Классификатор//Ид[. = \"$id\"]/../Наименование", self::$root);
            $info[] = $entries->item(0)->nodeValue;

            $entries = self::$xPath->query("//Классификатор//Ид[. = \"$id\"]/../Наименование/../../../Наименование", self::$root);
            $info[] = $entries->item(0)->nodeValue;
        }
        catch( Exception $e )
        {
            echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
        }

        return $info;
    }

    /**
     * @return string
     */
    public static function getCML2Object()
    {
        $file = file_get_contents( self::$cml2_path );

        return new SimpleXMLElement($file);
    }

    /**
     * @return mixed
     */
    public static function getClassifier()
    {
        return self::$classifier;
    }

    /**
     * @return mixed
     */
    public static function getGoods()
    {
        return self::$goods;
    }


}