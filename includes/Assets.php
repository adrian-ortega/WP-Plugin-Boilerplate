<?php

namespace GcKit;

use GcKit\Container;

class Assets
{
    public $isAdmin = false;

    /**
     * Holds all scripts to be enqueued when this class runs
     * @var array
     */
    protected $scripts = [];

    /**
     * Holds all styles to be enqueued when this class runs
     * @var array
     */
    protected $styles = [];

    /**
     * Holds the global plugin loader
     * @var Loader
     */
    protected $loader;

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

    protected $media;

    public function __construct(Container $container)
    {
        $this->loader   = $container->get('loader');
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
    public function add_script($handle, $source, $dependencies = [], $version = false, $footer = false) {
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
     * @param string $handle
     * @param string $source
     * @param array $dependencies
     * @param bool $version
     * @param string $media
     * @return $this
     */
    public function add_style($handle, $source, $dependencies = [], $version = false, $media = 'all') {
        $this->styles[] = [
            'handle' => $this->prefix . $handle,
            'source' => $this->base_dir .($this->isAdmin ? 'admin/' : 'frontend/') . 'assets/' . $source,
            'dependencies' => $dependencies,
            'version' => $version ? $version : $this->version,
            'media' => $media,
        ];
        return $this;
    }

    public function enqueue_media() {
        $this->media = true;
        return $this;
    }

    /**
     * Enqueues all scripts and styles
     */
    public function run()
    {
        $scripts = $this->scripts;
        $styles = $this->styles;
        $hook = ($this->isAdmin ? 'admin' : 'wp') . '_enqueue_scripts';
        $this->loader->add_action($hook, function() use($scripts, $styles) {
            if($this->isAdmin && $this->media) {
                wp_enqueue_media();
            }

            if(count($scripts) > 0) {
                foreach($scripts as $item) {
                    wp_enqueue_script(
                        $item['handle'],
                        $item['source'],
                        $item['dependencies'],
                        $item['version'],
                        $item['footer']
                    );
                }
            }

            if(count($styles) > 0) {
                foreach($styles as $item) {
                    wp_enqueue_style(
                        $item['handle'],
                        $item['source'],
                        $item['dependencies'],
                        $item['version'],
                        $item['media']
                    );
                }
            }
        });
    }
}