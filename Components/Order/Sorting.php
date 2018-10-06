<?php

namespace PrepayMatch\Components\Order\Filter;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Sorting
{
    /**
     * More about sorting in Shopware:
     * https://developers.shopware.com/developers-guide/rest-api/#filter,-sort,-limit,-offset
     *
     * @return array
     */
    public static function create()
    {
        return [
            [
                'property' => 'id',
                'direction' => 'ASC',
            ]
        ];
    }
}