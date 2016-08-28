<?php

namespace AOD;

class Loader
{
    /**
     * Array of filters to be registered
     * @var array
     */
    protected $filters = [];

    /**
     * Array of actions to be registered
     * @var array
     */
    protected $actions = [];

    /**
     * @var Container
     */
    protected $container;

    public function __construct(Container &$container)
    {
        $this->container = $container;
    }

    /**
     * Adds an action
     * @param string    $hook          the name of the hook we want to register to
     * @param callable  $callback      The callback function, either a closure, function string or object
     * @param integer   $priority      Priority of the action
     * @param integer   $accepted_args The number of accepted arguments for the callable
     */
    public function add_action($hook, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->add($this->actions, $hook, $callback, $priority, $accepted_args);
    }

    /**
     * Adds a filter
     * @param string    $hook          the name of the hook we want to register to
     * @param callable  $callback      The callback function, either a closure, function string or object
     * @param integer   $priority      Priority of the action
     * @param integer   $accepted_args The number of accepted arguments for the callable
     */
    public function add_filter($hook, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->add($this->filters, $hook, $callback, $priority, $accepted_args);
    }

    /**
     * Takes the filters or arrays by reference and adds an item to it
     * @param array    $hooks          the name of the hook we want to register to
     * @param string    $hook          the name of the hook we want to register to
     * @param callable  $callback      The callback function, either a closure, function string or object
     * @param integer   $priority      Priority of the action
     * @param integer   $accepted_args The number of accepted arguments for the callable
     */
    private function add(&$hooks, $hook, $callback, $priority, $accepted_args)
    {

        $hooks[] = [
            'hook' => $hook,
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args
        ];

    }

    /**
     * Registers all filters and actions
     * @return void
     */
    public function run()
    {
        foreach ( $this->filters as $hook ) {
            add_filter(
                $hook['hook'],
                $hook['callback'],
                $hook['priority'],
                $hook['accepted_args']
            );
        }

        foreach ( $this->actions as $hook ) {
            add_action(
                $hook['hook'],
                $hook['callback'],
                $hook['priority'],
                $hook['accepted_args']
            );
        }
    }
}