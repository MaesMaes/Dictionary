<?php
/**
 * Created by PhpStorm.
 * User: jscheq
 * Date: 08.04.17
 * Time: 13:00
 */

namespace liw\app;


class App
{
    /**
     * Строет модели и собирает JSON из import.xml и offers.xml
     */
    static public function createJSONData( )
    {
        self::createJSONFromDictionary();

        $cml2 = new CML2();
        $goods = $cml2::getGoods();
        $i = 0;
        $p = new Price();
        $goodsList = array();
        $producers = json_decode( file_get_contents( 'app/tmp/producers.json' ) );
        $colors = json_decode( file_get_contents( 'app/tmp/colors.json' ) );

        foreach ( $goods->{'Товар'} as $good )
        {
            $obj = new Good(
                (string)$good->{'Ид'},
                (string)$good->{'Наименование'},
                (string)$good->{'Артикул'},
                $cml2::findGroupByIDClassifier( $good->{'Группы'}->{'Ид'} )
            );

            $obj->producer = Good::getDictionaryValue( $producers, $obj->title );
            $obj->color = Good::getDictionaryValue( $colors, $obj->title );

            $obj->sizeAndPrice[$obj->size] = $p->findPriceByGoodID( $obj->ID1C );
            $obj->sizeAndTitles[$obj->size] = $obj->title;
            $obj->sizeAndIds[$obj->size] = $obj->ID1C;
            $obj->sizeAndAmount[$obj->size] = $p->findCntByGoodID( $obj->ID1C );

            $obj->basicUnit = $p->findBasicUnitByGoodID( $obj->ID1C );

            if( !Good::mergeGoods( $goodsList,  $obj ) )
                $goodsList[] = $obj;

            echo $i . "<br/>";
            $i++;
        }

        file_put_contents( 'app/tmp/ModelsList.json', json_encode( $goodsList )  );
    }

    /**
     * Посмотреть на JSON - файл (для отладки)
     *
     * @param $path
     */
    static public function showJSONData( $path )
    {
        $q = json_decode( file_get_contents( $path ) );

        ?>
        <pre>
            <? print_r( $q ); ?>
        </pre>
        <?
    }

    /**
     * Берет существующие модели, апдейтит их,
     * беря инфу с сайта и записывает в файл ModelsListUpdated.json
     */

    static public function UpdateModelsFromSite()
    {
        $links = json_decode( file_get_contents( 'app/tmp/links.json' ) );
        $url = 'http://t-military.ru/';

        foreach ( $links as $link )
        {
            $html = file_get_contents( $url . $link );

            //TODO написать парсер)
        }
    }

    // Обновляет данные из словарей .data в .json
    static private function createJSONFromDictionary()
    {
        $q = file( 'app/tmp/producers.data' );
        $w = file( 'app/tmp/colors.data' );

        file_put_contents( 'app/tmp/producers.json', json_encode( $q )  );
        file_put_contents( 'app/tmp/colors.json', json_encode( $w )  );
    }

    /**
     * Фиксит отсутствующие пробелы между цветом и чем-то в готовом json
     */
    static public function fixSpace()
    {
        $q = json_decode( file_get_contents( 'app/tmp/ModelsList.json' ) );
        foreach ( $q as $good )
        {
            if( $p = stripos( $good->name, $good->color ) !== false )
            {
                if( $good->name[$p + count( $good->color )] != ' ')
                {
                    $good->name = substr_replace( $good->name, ' ', $p + count( $good->color ), 0 );
                }
            }
        }
    }
}