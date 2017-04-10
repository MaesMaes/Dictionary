<?php
/**
 * Created by PhpStorm.
 * User: jscheq
 * Date: 28.03.17
 * Time: 14:14
 */

namespace liw\app;

/**
 * Для работы с дополнительными свойствами товара
 *
 * Class SearchProp
 * @package liw\app
 */
class SearchProp
{
    private static $sizes = ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', '3XL', '2XL', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46'];

    public $title;

    /**
     * SearchProp constructor.
     * @param $title
     */
    public function __construct( $title )
    {
        $this->title = $title;
    }

    /**
     * Возвращает искомое свойство в зависимости от переданных параметров.
     *
     * @param $r - что будем искать?) - Name, Color, Size
     * @param null $o
     * @return string
     */
    public function getProperty( $r, $o = null )
    {
        $prop = "";
        $input = [];

        switch ($r)
        {
            case "Name":
                if ( !empty( $o->title ) )
                {
                    $prop = $o->title;

                    if( !empty( $o->color ) )
                    {
                        $prop = str_replace( $o->color, " ", $prop );
                        $prop = str_replace( mb_strtolower( $o->color ), " ", $prop );
                    }

                    if( !empty( $o->size ) )
                    {
                        $prop = str_replace( '/' . $o->size . '/', " ", $prop );
                    }

                    $prop = str_replace( 'Ст', " ", $prop );
                    $prop = str_replace( ' Б', " ", $prop );

                    $prop = str_replace( '/', " ", $prop );
                    //$prop = str_replace( '\\', "", $prop );

                    $prop = str_replace( '  ', " ", $prop );
                    $prop = str_replace( '   ', " ", $prop );
                    $prop = str_replace( '.', " ", $prop );
                    $prop = str_replace( '(', " ", $prop );
                    $prop = str_replace( ')', " ", $prop );
                }
                $prop = preg_replace( "/\s{2,}/", " ", $prop );
                return trim( $prop );
                break;
            case "Size":
                $input = self::$sizes;
                break;
        }

        foreach ( $input as $c )
        {
            if(    stripos( $this->title, '/' . $c . '/' )
                || stripos( $this->title, '/ ' . $c . ' /' )
                || stripos( $this->title, ' ' . $c . ' ' )
                || stripos( $this->title, ' ' . $c . '/' )
                || stripos( $this->title, '/' . $c . "\n\r" )
                || stripos( $this->title, ' ' . $c . "\n\r" ) )
            {
                $prop = $c;
                return (string)$prop;
            }
        }

        return $prop;
    }
}