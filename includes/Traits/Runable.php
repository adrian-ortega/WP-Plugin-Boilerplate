<?php

namespace AOD\Traits;

use AOD\Container;
use AOD\Loader;

trait Runable
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Loader|null
     */
    protected $loader;

    public function __construct(Container &$container)
    {
        $this->init($container);
    }

    /**
     * Sets the container and loader. Can be used when overriding the __construct method.
     * @param Container $container
     * @return $this
     */
    protected function init(Container $container)
    {
        $this->container = $container;
        $this->loader = $container->get('loader');
        return $this;
    }

    /**
     * @return Loader|null
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param string|null $name
     * @return mixed|null
     */
    public function get($name = null)
    {
        return $this->getContainer()->get($name);
    }

    abstract public function run();
}