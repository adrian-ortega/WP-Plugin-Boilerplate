<?php

namespace AOD;

use AOD\Container;
use AOD\Traits\Runable;

class Assets
{
    use Runable;

    /**
     * Sets the Admin Flag
     * @var bool
     */
    public $isAdmin = false;

    /**
     * Holds all scripts to be enqueued when this class runs
     * @var array
     */
    protected $scripts = [];

    /**
     * Holds all javascript objects that need to be loaded before a specific script
     * @var array
     */
    protected $localizedObjects = [];

    /**
     * Holds all styles to be enqueued when this class runs
     * @var array
     */
    protected $styles = [];

    /**
     * Used to store the plugin base url path
     * @var string
     */
    protected $base_dir;

    /**
     * Stores the plugin prefix, this is so that all scripts are unique and names are tied to this
     * plugin
     * @var string
     */
    protected $prefix;

    /**
     * Stores the plugin version
     * @var mixed
     */
    protected $version;

    /**
     * WP Media flag
     * @var bool
     */
    protected $media = false;

    public function __construct(Container &$container)
    {
        $this->init($container);

        $this->base_dir = $container->get('plugin_url');
        $this->version  = $container->get('plugin_version');
        $this->prefix   = sanitize_title($container->get('plugin_name') . '_');
    }

    /**
     * Sets the flag to include scripts on the admin screen
     * @return $this
     */
    public function isAdmin()
    {
        $this->isAdmin = true;
        return $this;
    }

    /**
     * @param string $handle
     * @param string $source
     * @param array $dependencies
     * @param bool $version
     * @param bool $footer
     * @return $this
     */
    public function addScript($handle, $source, $dependencies = [], $version = false, $footer = false) {
        $this->scripts[] = [
            'handle' => $this->prefix . $handle,
            'source' => $this->base_dir .($this->isAdmin ? 'admin/' : 'frontend/') . 'assets/' . $source,
            'dependencies' => $dependencies,
            'version' => $version ? $version : $this->version,
            'footer' => $footer,
        ];

        return $this;
    }

    /**
     * Registers a javascript object
     * @param string $scriptHandle
     * @param string $objName
     * @param array $objData
     * @return $this
     */
    public function localObject($scriptHandle, $objName, array $objData)
    {
        $this->localizedObjects[] = [
            'handle' => $this->prefix . $scriptHandle,
            'object' => $objName,
            'data' => $objData
        ];
        return $this;
    }

    /**
     * @param string $handle
     * @param string $source
     * @param array $dependencies
     * @param bool $version
     * @param string $media
     * @return $this
     */
    public function addStyle($handle, $source, $dependencies = [], $version = false, $media = 'all') {
        $this->styles[] = [
            'handle' => $this->prefix . $handle,
            'source' => $this->base_dir .($this->isAdmin ? 'admin/' : 'frontend/') . 'assets/' . $source,
            'dependencies' => $dependencies,
            'version' => $version ? $version : $this->version,
            'media' => $media,
        ];
        return $this;
    }

    /**
     * Sets the Media flag
     * @return $this
     */
    public function enqueueMedia() {
        $this->media = true;
        return $this;
    }

    /**
     * Enqueues all scripts and styles
     */
    public function run()
    {
        $this->getLoader()->addAction(($this->isAdmin ? 'admin' : 'wp') . '_enqueue_scripts', function() {
            if($this->isAdmin && $this->media) {
                wp_enqueue_media();
            }

            if(count($this->scripts) > 0) {
                foreach ($this->scripts as $item) {
                    wp_register_script(
                        $item['handle'],
                        $item['source'],
                        $item['dependencies'],
                        $item['version'],
                        $item['footer']
                    );
                }
            }
            if(count($this->localizedObjects) > 0) {
                foreach($this->localizedObjects as $obj) {
                    wp_localize_script($obj['handle'], $obj['object'], $obj['data']);
                }
            }

            if(count($this->scripts) > 0) {
                foreach($this->scripts as $item) {
                    wp_enqueue_script($item['handle']);
                }
            }

            if(count($this->styles) > 0) {
                foreach($this->styles as $item) {
                    wp_register_style(
                        $item['handle'],
                        $item['source'],
                        $item['dependencies'],
                        $item['version'],
                        $item['media']
                    );
                }

                foreach($this->styles as $item) {
                    wp_enqueue_style($item['handle']);
                }
            }
        });
    }
}