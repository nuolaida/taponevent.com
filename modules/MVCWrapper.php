<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 11/6/17
 * Time: 9:56 AM
 */

class MVCWrapper
{
    /**
     * @var array
     */
    private $page_special_config;

    /**
     * @var Smarty
     */
    private $smarty;

    /**
     * @var array
     */
    private $syspageChanges;

    /**
     * mvcWrapper constructor.
     * @param $page_special_config
     * @param Smarty $smarty
     */
    public function __construct($page_special_config, $smarty)
    {
        $this->page_special_config = $page_special_config;
        $this->smarty = $smarty;

        $this->syspageChanges = [];
    }

    /**
     * @return mixed
     */
    public function getPageSpecialConfig()
    {
        return $this->page_special_config;
    }

    /**
     * @return Smarty
     */
    public function getSmarty()
    {
        return $this->smarty;
    }

    /**
     * @param $propertyName
     * @param $value
     * @return $this
     */
    public function addSyspageChange($propertyName, $value)
    {
        $this->syspageChanges[$propertyName] = $value;
        return $this;
    }

    /**
     * Apply syspage changes to given array
     *
     * @param $syspage
     * @return array
     */
    public function applySyspageChanges($syspage)
    {
        if (!is_array($syspage)) {
            $syspage = [];
        }

        if (!empty($this->syspageChanges)) {
            foreach ($this->syspageChanges as $property => $value) {
                $syspage[$property] = $value;
            }
        }

        return $syspage;
    }
}