<?php
/**
 * Created by PhpStorm.
 * User: jscheq
 * Date: 24.03.17
 * Time: 15:14
 */

namespace liw\app;

/**
 * Модель товара
 *
 * Class Good
 * @package liw\app
 */
class Good
{
    public $ID1C;
    public $title;
    public $name;
    public $desc;
    public $color;
    public $vendor;
    public $vendors = array();
    public $images = array();
    public $sizeAndPrice = array();
    public $group = array();
    public $size;
    public $producer;
    public $catalog = array();
    static private $catalogDictionary = array(
        'Одежда' => [
            'Аляски',
            'куртки',
            'жилеты',
            'Брюки',
            'джинсы',
            'костюмы',
            'свитера',
            'толстовки',
            'футболки',
            'эголовные уборы',
            'рубашки',
            'лайнеры',
            'термобелье',
            'шорты',
            'плащи',
            'Детское',
            'Брюки',
            'Головные уборы',
            'Футболки',
            'Куртки',
        ],
        'Детское' => [
            'Брюки',
            'Головные уборы',
            'Футболки',
            'Куртки',
        ],
        'Обувь' => [
            'ботинки',
            'сапоги',
            'полусапоги'
        ],
        'Аксессуары' => [
            'очки',
            'часы',
            'Сумки',
            'шарфы',
            'ремни',
            'перчатки',
            'прочее',
            'баулы',
            'рюкзаки',
            'Система "Molle"',
            'Подсумки поясные',
            'Носки'
        ],
        'Оружие' => [
            'пневматика',
            'ножи',
            'аксессуары',
            'Экипировка',
            'фонари',
            'маскировочная краска',
            'компасы',
            'кобура',
            'аксессуары',
            'защита'
        ]
    );

    // Количество инстансов
    private static $cntGoods = 0;

    /**
     * Good constructor.
     * @param $ID1C
     * @param $title
     * @param $vendor
     * @param array $group
     */
    public function __construct( $ID1C, $title, $vendor, array $group )
    {
        $this->ID1C = $ID1C;
        $this->title = $title;
        $this->vendor = $vendor;
        $this->vendors[] = $this->vendor;
        $this->group = $group;

        $prop = new SearchProp( $this->title );

        $this->size = $prop->getProperty( 'Size' );
        $this->name = $prop->getProperty( 'Name', $this );
        $this->desc = "";
        $this->images = [];
        $this->sizeAndPrice = [];

        $this->sizes = array();
        $this->sizes[] = $this->size;

        $this->setCatalog();

        ++self::$cntGoods;
    }

    /**
     * Ищет присутствие товара в каком либо каталоге и устанавливает свойство
     * если находит
     */
    public function setCatalog()
    {
        foreach( self::$catalogDictionary as $key => $category )
        {
            foreach( $category as $cat => $subCategory )
            {
                if ( stripos( $this->title, trim( $subCategory ) ) !== false || in_array( $subCategory, $this->group ))
                {
                    $this->catalog[$key] = $subCategory;
                    break 2;
                }
            }
        }
    }

    /**
     * @return int
     */
    public static function getCntGoods()
    {
        return self::$cntGoods;
    }

    /**
     * Проверяет есть ли в текущем массиве похожие товары
     * и если есть апдейтит свойства размера
     *
     * @param array $goodList
     * @param $obj
     * @return bool
     */
    public static function mergeGoods( array $goodList, $obj )
    {
        foreach ( $goodList as $good )
        {
            $percent = 0;
            similar_text( $good->name, $obj->name, $percent );

            if( $percent > 95 && $obj->color == $good->color )
            {
                if( !in_array( $obj->size, $good->sizes ) )
                {
                    $good->sizes[] = $obj->size;
                    $good->vendors[] = $obj->vendor;

                    if( !is_numeric( $obj->size ) )
                        $good->sizeAndPrice = array_merge( $good->sizeAndPrice, $obj->sizeAndPrice );
                    else
                    {
                        $good->sizeAndPrice[key( $obj->sizeAndPrice )] = $obj->sizeAndPrice[key( $obj->sizeAndPrice )];
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Ищет и возвращает свойство из словаря или null
     *
     * @param array $ar
     * @param $title
     * @return mixed|null
     */
    public static function getDictionaryValue( array $ar, $title )
    {
        foreach( $ar as $p )
        {
            if ( stripos( $title, trim( $p ) ) !== false )
            {
                return $p;
            }
        }

        return null;
    }



}