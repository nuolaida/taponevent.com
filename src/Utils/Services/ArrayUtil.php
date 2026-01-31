<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 3/20/18
 * Time: 4:39 PM
 */

namespace BestPub\Utils\Services;


class ArrayUtil
{
    /**
     * Group items by key value
     *
     * @param array $items
     * @param $key
     * @param bool $keepIndex
     * @return array
     */
    public static function groupBy(array $items, $key, $keepIndex = false) {
        $groupedItems = [];
        foreach($items as $itemKey => $item) {
            if (!isset($groupedItems[$item[$key]])) {
                $groupedItems[$item[$key]] = [];
            }

            if($keepIndex) {
                $groupedItems[$item[$key]][$itemKey] = $item;
            } else {
                $groupedItems[$item[$key]][] = $item;
            }
        }

        return $groupedItems;
    }

    /**
     * Change key with given property value
     *
     * @param array $items
     * @param $key
     * @return array
     */
    public static function changeKey(array $items, $key)
    {
        $result = [];
        foreach($items as $itemKey => $item) {
            $result[$item[$key]] = $item;
        }

        return $result;
    }

    /**
     * Find element with biggest value by given key
     *
     * @param $elements
     * @param $key
     * @return mixed
     */
    public static function getElementWithBiggestValue($elements, $key)
    {
        $dataStats = $elements;
        usort($dataStats, function($a, $b) use ($key) { return $b[$key] - $a[$key];});
        return $dataStats[0];
    }

    /**
     * Sort elements by keys
     *
     * @param $elements
     * @param $keys
     * @return mixed
     */
    public static function sortBy($elements, array $keys)
    {
        $dataStats = $elements;
        usort($dataStats, function($a, $b) use ($keys) {
            foreach ($keys as $key) {
                if($b[$key] !== $a[$key]) {
                    return $b[$key] - $a[$key];
                }
            }
        });

        return $dataStats[0];
    }
}