<?php
/**
 * Created by PhpStorm.
 * User: polidog
 * Date: 2017/05/12
 * Time: 19:13
 */

namespace Polidog\ControllerFilterBundle\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
final class Filter implements FilterInterface
{
    const TYPE_BEFORE = 'before';

    const TYPE_AFTER = 'after';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $service;

    /**
     * Filter constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        if (isset($params['value'])) {
            $this->type = (string)$params['value'];
        }

        foreach (['method','service','type'] as $target) {
            if (isset($params[$target])) {
                $this->$target = $params[$target];
            }
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getService(): string
    {
        return $this->service;
    }

}
