<?php

namespace AOD;

use AOD\Traits\Runable;

class Localization
{
    use Runable;

    /**
     * @var string
     */
    private $domain;

    /**
     * The location of the Languages directory;
     * @var string
     */
    private $path;

    /**
     * Loads the plugin domain for translations
     */
    public function loadTextDomain()
    {
        load_plugin_textdomain($this->domain, false, $this->path);
    }

    /**
     * Sets the path of the languages directory
     * @param null|string $path
     * @return $this
     */
    protected function _setPath($path = null)
    {
        if(empty($path)) {
            $path = __DIR__ . '/Languages';
        }
        $this->path = $path;
        return $this;
    }

    /**
     * Sets the domain name for the plugin
     * @param string|$domain
     * @return $this
     */
    protected function _setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * Returns the text domain for the plugin
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Run
     */
    public function run()
    {
        $this->_setPath($this->get('plugin_path') . '/languages');
        $this->_setDomain($this->get( 'plugin_text_domain' ));

        $this->getLoader()->addAction('plugins_loader', [$this, 'loadTextDomain']);
    }
}