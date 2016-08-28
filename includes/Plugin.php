<?php


namespace AOD;

class Plugin
{
    /**
     * @var Plugin
     */
    protected static $instance;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Loader
     */
    protected $loader;

    /**
     * Used to store dependency directory paths
     * to be loaded later
     * @var array
     */
    protected $fileDependencies = [];

    protected function __construct($pluginFile = null)
    {
        if(empty($pluginFile)){
            $pluginFile = __DIR__ . '../';
        }

        $this->container = new Container([
            'plugin_path' => plugin_dir_path( $pluginFile ),
            'plugin_url' => plugin_dir_url( $pluginFile ),
            'loader' => function(Container $c) {
                return new Loader($c);
            }
        ]);



    }

    /**
     * Returns the singleton Instance of this plugin
     * @param  string $pluginFile the base path of the current plugin this boiler plate is being used for
     * @return Plugin
     */
    public static function getInstance($pluginFile = null)
    {
        if(empty(self::$instance) && !empty($pluginFile)) {
            self::$instance = new static( $pluginFile );
        } else {
            throw new \InvalidArgumentException('`$pluginFile` cannot be empty when first instantiating the Plugin class');
        }

        return self::$instance;
    }

    /**
     * Returns the plugin container
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Returns the global action and filter loader
     * @return Loader
     */
    public function getLoader()
    {
        return $this->get('loader');
    }

    /**
     * Returns an item from the container
     * @param  string $item    The named key of the item stored in the container
     * @return mixed
     */
    public function get($item)
    {
        return $this->getContainer()->get($item);
    }

    public function with($key, $item)
    {
        $this->getContainer()->set($key, $item);
    }

    /**
     * Sets the plugin name and version
     * @param string $name
     * @param string $version
     * @return $this
     */
    public function init($name = "AOD WP Plugin Boilerplate", $version = '1.0.0')
    {
        $this->with('plugin_name', $name);
        $this->with('plugin_version', $version);
        return $this;
    }

    /**
     * Runs the app
     */
    public function run()
    {
        foreach( $this->getContainer()->getCallables() as $name => $callable) {

            // Check to see if this class object has a run method and instantiate it.
            if($name != 'loader' && method_exists($callable, 'run')) {
                $callable->run();
            }
        }

        $this->getLoader()->run();
    }
}