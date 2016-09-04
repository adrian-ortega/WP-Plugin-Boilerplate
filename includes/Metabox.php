<?php

namespace AOD;

class Metabox
{

    const METAKEY = 'undefined';
    const USE_SINGLE_KEYS = false;

    /**
     * The name of the box, used for the title and html ID
     * @var string
     */
    protected $name = 'GC Metabox';

    /**
     * The html ID used by the metabox html elements
     * @var string
     */
    protected $htmlID;

    /**
     * default key value pairs for this metabox
     * @var array
     */
    public static $defaults = [];

    /**
     * The post type this metabox is added to
     * @var string
     */
    protected $postType = 'post';

    /**
     * Can be either normal, side, advanced
     * @var string
     */
    protected $context = 'normal';

    /**
     * Can be either high or low
     * @var string
     */
    protected $priority = 'high';

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string|null
     */
    protected $textDomain;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->textDomain = $this->container->get('plugin_text_domain');
        $this->htmlID = sanitize_title($this->name. ' aod metabox');
    }

    /**
     * Checks to see if we're in the correct screen to load scripts
     * @return bool
     */
    public function correctScreen()
    {
        global $pagenow, $typenow;

        if (empty($typenow) && !empty($_GET['post'])) {
            $post = get_post($_GET['post']);
            $typenow = $post->post_type;
        }

        if (is_admin() && $typenow == $this->postType) {
            return !!($pagenow == 'post-new.php' || $pagenow == 'post.php');
        }
        return false;
    }
    /**
     * Adds the metabox to WordPress
     */
    public function add()
    {
        add_meta_box(
            $this->htmlID,
            $this->name,
            [$this, 'display'],
            $this->postType,
            $this->context,
            $this->priority
        );
    }

    /**
     * @param int $post_id
     * @param array $defaults
     * @param null|string $parent_key
     */
    private function _saveSingleKeys($post_id, $defaults = [], $parent_key = null)
    {
        foreach($defaults as $dkey => $dvalue) {
            $mk = $parent_key . '_' . $dkey;
            if(is_array($dvalue)) {
                $this->_saveSingleKeys($post_id, $dvalue, $mk);
            } else {
                $value = isset($_POST[$mk]) ? $_POST[$mk] : null;
                if(!empty($value)) {
                    update_post_meta($post_id, $mk, $value);
                } else {
                    delete_post_meta($post_id, $mk);
                }
            }
        }
    }

    /**
     * Saves the post's meta data created by this metabox
     * @param $post_id
     * @return void|int
     */
    public function save($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
        $current_screen = get_current_screen();

        /** @var Metabox $class */
        $class = get_called_class();
        $correct_screen = in_array($current_screen->post_type, (array) $this->postType);

        if ($_POST && $correct_screen) {

            if($class::USE_SINGLE_KEYS) {
                $this->_saveSingleKeys($post_id, $class::$defaults, $class::METAKEY);
            } else {
                $value = $_POST[$class::METAKEY];

                if (!empty($value)) {
                    update_post_meta($post_id, $class::METAKEY, $value);
                } else {
                    delete_post_meta($post_id, $class::METAKEY);
                }
            }
        }

        return $post_id;
    }

    /**
     * The method that takes care of diplaying the form
     * @param \WP_Post $post
     */
    public function display(\WP_Post $post) { }


    /**
     * Sets the name of the metabox
     * @param string $name
     * @return $this
     */
    public function setName($name = '')
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Sets the post type of the metabox
     * @param string $type
     * @return $this
     */
    public function setPostType($type)
    {
        $this->postType = $type;
        return $this;
    }

    /**
     * Sets the context of the metabox
     * @param string $context 'normal', 'side', 'advanced'
     * @return  $this
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Sets the default values for this metabox's fields.
     * @param array $defaults
     * @return  $this
     */
    public function setDefaults($defaults = array()) {
        /** @var Metabox $class */
        $class = get_called_class();
        $class::$defaults = $defaults;
        return $this;
    }

    /**
     * Sets the HTML ID of the metabox
     * @param string $id
     * @return $this
     */
    public function setHtmlID($id = '')
    {
        $this->htmlID = empty($id) ? sanitize_title($this->name) : esc_attr($id);
        return $this;
    }

    /**
     * Returns an HTML ID for an element.
     * @param int|string $name
     * @return string
     */
    protected function getHtmlID($name)
    {
        /** @var Metabox $class */
        $class = get_called_class();
        $args = func_get_args();
        $sub = '';

        if (isset($args[1])) {
            for ($i = 1; $i < count($args); $i++) {
                $sub .= "_{$args[$i]}";
            }
        }
        return $class::METAKEY . '_' . $name . $sub;
    }

    /**
     * Print wrapper for $this->getHtmlID()
     * @param  string $name
     * @return void
     */
    protected function htmlID($name = '') {
        echo call_user_func_array([$this, 'getHtmlID'], func_get_args());
    }

    /**
     * Returns the Input name for the element.
     * @param int|string $name
     * @return string
     */
    protected function getInputName($name)
    {
        /** @var Metabox $class */
        $class = get_called_class();
        $args = func_get_args();
        $sub = '';
        if (isset($args[1])) {
            for ($i = 1; $i < count($args); $i++) {
                $sub .= $class::USE_SINGLE_KEYS ? "_{$args[$i]}" : "[{$args[$i]}]";
            }
        }

        return $class::METAKEY . ($class::USE_SINGLE_KEYS ? "_{$name}" :"[{$name}]") . $sub;
    }

    /**
     * Print wrapper for getInputName()
     * @param $name
     */
    protected function inputName($name)
    {
        echo call_user_func_array([$this, 'getInputName'], func_get_args());
    }

    /**
     * Returns the meta data for a post based on the metakey of a child class
     * @param int|null|\WP_Post $post
     * @return mixed
     */
    protected function getMeta($post = null)
    {
        if(is_object($post)){
            $post = $post->ID;
        }

        /** @var Metabox $class */
        $class = get_called_class();
        return get_post_meta($post, $class::METAKEY, true);
    }

    /**
     * @param array $a
     * @param array|null $b
     *
     * @return array
     */
    protected function parseMeta(&$a, $b)
    {
        $a = (array) $a;
        $b = (array) $b;
        $result = $b;
        foreach($a as $k => $v) {
            if ( is_array( $v ) && isset( $result[ $k ] ) ) {
                $result[ $k ] = $this->parseMeta( $v, $result[ $k ] );
            } else {
                $result[ $k ] = $v;
            }
        }
        return $result;
    }

    /**
     * @param int|null|\WP_Post $post
     * @param array $defaults
     * @param null|string $parent_key
     * @return array
     */
    private function _getSingleKeyMeta($post = null, $defaults = [], $parent_key = null) {
        $meta = [];
        if(!$parent_key) {
            /** @var Metabox $class */
            $class = get_called_class();
            $parent_key = $class::METAKEY;
        }

        if(is_object($post)){
            $post = $post->ID;
        }

        foreach ($defaults as $key => $value) {
            $_mk = $parent_key . '_' . $key;
            if(is_array($value)) {
                $meta[$key] = $this->_getSingleKeyMeta($post, $value, $_mk);
            } else {
                $meta[$key] = get_post_meta($post, $_mk, true);
            }
        }
        return $this->parseMeta($meta, $defaults);
    }

    /**
     * @param int|null|\WP_Post $post
     * @return array
     */
    protected function getMetaWithDefaults($post)
    {
        /** @var Metabox $class */
        $class = get_called_class();
        return $class::USE_SINGLE_KEYS ?
            $this->_getSingleKeyMeta($post, $class::$defaults) :
            $this->parseMeta($class::$defaults, $this->getMeta($post));
    }

    public function run() {
        if(is_admin() ){
            $loader = $this->container->get('loader');

            $loader->add_action('admin_init', [$this, 'add']);
            $loader->add_action('save_post', [$this, 'save']);
        }
    }

    /**
     * Returns either all meta data or one by key if passed.
     * @param  \WP_Post|int $post
     * @param  string $key
     * @return mixed
     */
    public static function meta($post, $key = null)
    {
        if(is_object($post)) {
            $post = $post->ID;
        }

        if(empty($post)) {
            return false;
        }

        $class = get_called_class();

        /** @var Metabox $metabox */
        $metabox = new $class();
        $values = $metabox->getMetaWithDefaults($post);

        if(!empty($key) && isset($values[$key])) {
            return $values[$key];
        }

        return $values;
    }
}