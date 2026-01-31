<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 12/19/17
 * Time: 9:49 AM
 */

namespace BestPub\Utils\Services\Environment;


use BestPub\Utils\Services\View\Location\Domain;
use BestPub\Utils\Services\View\Location\Location;
use BestPub\Utils\Services\View\Location\Main;
use RuntimeException;

class Admin implements Environment
{
    private $hierarchy;

    private static $CONFIG_KEYS = [
        'header_html' => 'admin_header_html',
        'footer_html' => 'admin_footer_html',
        'styles_clear' => 'admin_styles_clear',
        'custom_styles' => 'admin_styles'
    ];

    /**
     * Admin constructor.
     */
    public function __construct()
    {
        $this->hierarchy = [
            new Domain('/admin'),
            new Main('admin/modules/')
        ];
    }

    /**
     * @return Location[]
     */
    public function getViewsHierarchy()
    {
        return $this->hierarchy;
    }

    /**
     * Get config key for particular value
     *
     * @param $key
     * @return string
     * @throws \RuntimeException
     */
    public function getConfigKey($key)
    {
        if (!isset(self::$CONFIG_KEYS[$key])) {
            throw new RuntimeException('Config key "' . $key . '" is missing');
        }

        return self::$CONFIG_KEYS[$key];
    }
}