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

class Web implements Environment
{
    private $hierarchy;

    private static $CONFIG_KEYS = [
        'header_html' => 'header_html',
        'footer_html' => 'footer_html',
        'styles_clear' => 'styles_clear',
        'default_css' => 'default_css',
        'custom_styles' => 'styles',
        'header_title' => 'header_title',
        'favicon' => 'favicon',
        'rss' => 'rss',
        'protocol' => 'protocol',
        'domain' => 'domain',
        'title_ads' => 'title_ads',
        'default_sharing_image' => 'default_sharing_image',
        'site_name' => 'name',
        'google_client_id' => 'google_client_id',
        'facebook_app_id' => 'facebook_app_id',
        'facebook_graph_version' => 'facebook_graph_version'
    ];

    /**
     * Web constructor.
     */
    public function __construct()
    {
        if (extension_loaded('newrelic')) {
            newrelic_add_custom_tracer('BestPub\\Utils\\Services\\Environment\\Web::getConfigKey');
        }
        $this->hierarchy = [
            new Domain(),
            new Main()
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