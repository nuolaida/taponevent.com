<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 12/19/17
 * Time: 1:24 PM
 */

namespace BestPub\Utils\Services;

use BestPub\Utils\Services\View\Location\Main;
use Smarty;

class View extends Smarty
{
    /**
     * @var array
     */
    private $hierarchy;

    public function __construct()
    {
        if (extension_loaded('newrelic')) {
            newrelic_add_custom_tracer('BestPub\\Utils\\Services\\View::locateTemplate');
            newrelic_add_custom_tracer('BestPub\\Utils\\Services\\View::fetch');
        }

        parent::__construct();
    }

    /**
     * Get cache id for template
     *
     * @param $templateData
     * @param $cacheId
     * @return mixed|string
     */
    private function getCacheId($templateData, $cacheId)
    {
        if ($templateData['isMain'] === false) {
            $keyword = Config::getDomainConfigValue('keyword');
            return $cacheId ? ($cacheId . '|' . $keyword) : $keyword;
        }

        return $cacheId;
    }

    /**
     * Get compile id for template
     *
     * @param $templateData
     * @param $compileId
     * @return mixed|string
     */
    private function getCompileId($templateData, $compileId)
    {
        if ($templateData['isMain'] === false) {
            $keyword = Config::getDomainConfigValue('keyword');
            return $compileId ? ($compileId . '|' . $keyword) : $keyword;
        }

        return $compileId;
    }
    /**
     * Fetch template
     *
     * @param null|string $template
     * @param null|string $cacheId
     * @param null|string $compileId
     * @param null|string $parent
     * @return string
     * @throws \Exception
     */
    public function fetch($template = null, $cacheId = null, $compileId = null, $parent = null)
    {
        $templateData = $this->locateTemplate($template);
        return parent::fetch(
            $templateData['path'],
            $this->getCacheId($templateData, $cacheId),
            $this->getCompileId($templateData, $compileId),
            $parent
        );
    }

    /**
     * Display template
     *
     * @param null $template
     * @param null $cacheId
     * @param null $compileId
     * @param null $parent
     * @throws \RuntimeException
     */
    public function display($template = null, $cacheId = null, $compileId = null, $parent = null)
    {
        $templateData = $this->locateTemplate($template);
        return parent::display(
            $templateData['path'],
            $this->getCacheId($templateData, $cacheId),
            $this->getCompileId($templateData, $compileId),
            $parent
        );
    }

    /**
     * @param null $template
     * @param null $cacheId
     * @param null $compileId
     * @param null $parent
     * @return bool
     * @throws \RuntimeException
     */
    public function isCached($template = null, $cacheId = null, $compileId = null, $parent = null)
    {
        $templateData = $this->locateTemplate($template);
        return parent::isCached(
            $templateData['path'],
            $this->getCacheId($templateData, $cacheId),
            $this->getCompileId($templateData, $compileId),
            $parent
        );
    }

    /**
     * Locate template in hierarchy
     *
     * @param $template
     * @return array
     * @throws \RuntimeException
     */
    public function locateTemplate($template)
    {
        if ($this->hierarchy === null) {
            $this->generateHierarchy();
        }

        foreach ($this->hierarchy as $location) {
            $templatePath = $location['prefix'] . $template;
            if ($this->templateExists($templatePath)) {
                return ['path' => $templatePath, 'isMain' => $location['isMain']];
            }
        }

        throw new \RuntimeException('Missing template: ' . $template);
    }

    /**
     * Creates hierarchy for current environment
     *
     * @throws \RuntimeException
     */
    private function generateHierarchy()
    {
        foreach (Context::getEnvironment()->getViewsHierarchy() as $location) {
            $isMain = $location instanceof Main;
            if ($isMain) {
                $templatePath = $location->getDirectory();
            } else {
                $templatePath = 'file:' . SERVER_PATH . $location->getDirectory() . '/';
            }

            $this->hierarchy[] = ['prefix' => $templatePath, 'isMain' => $isMain];
        }
    }
}