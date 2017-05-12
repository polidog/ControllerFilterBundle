<?php
/**
 * Created by PhpStorm.
 * User: polidog
 * Date: 2017/05/13
 * Time: 0:40
 */

namespace Polidog\ControllerFilterBundle\Annotations;


interface FilterInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @return string
     */
    public function getService();

    /**
     * @return boolean
     */
    public function isServiceFilter();

    /**
     * @return boolean
     */
    public function isMethodFilter();
}
