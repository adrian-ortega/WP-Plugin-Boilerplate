<?php

namespace AOD;
use \ArrayAccess;

class Container implements ArrayAccess
{
    /**
     * Stores ALL items
     * @var array
     */
    protected $items = [];


    /**
     * @var array
     */
    protected $cache = [];

    public function __construct( array $items = [] )
    {
        foreach ( $items as $key => $item ) {
            $this->offsetSet( $key, $item );
        }
    }

    /**
     * Add an item to the container
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet( $offset, $value )
    {
        $this->items[$offset] = $value;
    }

    /**
     * @param string $offset
     * @return mixed|null
     */
    public function offsetGet( $offset )
    {
        // Return null if the item key does not exist
        if ( !$this->has( $offset ) ) {
            return null;
        }

        // Check to see if the item has been cached before
        // trying to set and/or instantiate it
        if ( isset( $this->cache[$offset] ) ) {
            return $this->cache[$offset];
        }

        // Since the item exists, we will save it in a temporary
        // variable to check if its a callable
        $item = $this->items[$offset];

        // Since this allows to store callables, like a closure
        // or an object's method, we check to see if its callable
        // before we try to set it into the cache items
        if( $item instanceof \Closure || is_callable($item) )  {

            // Instantiate the callable and pass this container into it.
            $item = call_user_func_array( $this->items[$offset], [ $this ] );
        }

        // Finally, its saved into cache for future calls
        $this->cache[$offset] = $item;

        // Item is returned for the first time
        return $item;
    }

    /**
     * Removes an item from the container
     * @param string $offset
     */
    public function offsetUnset( $offset )
    {
        if ( $this->has( $offset ) ) {
            unset( $this->items[$offset] );
        }
    }

    /**
     * Check to see if an item exists
     * @param string $offset
     * @return bool
     */
    public function offsetExists( $offset )
    {
        return isset( $this->items[$offset] );
    }

    /**
     * Wrapper for offsetGet() Magic method to retrieve an item as a property
     * @param string $property
     * @return mixed|null
     */
    public function __get( $property )
    {
        return $this->offsetGet( $property );
    }

    ////////////////////////////////////////////////////////////////////////
    //  API Wrappers
    ////////////////////////////////////////////////////////////////////////

    /**
     * Wrapper for offsetSet
     * @param $key
     * @param $item
     */
    public function set($key, $item)
    {
        $this->offsetSet($key, $item);
    }

    /**
     * Wrapper for offsetExists, Check to see if an item exists
     * @param string $offset
     * @return bool
     */
    public function has( $offset )
    {
        return $this->offsetExists( $offset );
    }

    /**
     * Wrapper for offsetGet
     * @param $item
     * @return mixed|null
     */
    public function get( $item )
    {
        return $this->offsetGet( $item );
    }

    public function getCallables()
    {
        $callables = [];

        // If items have not been instantiated, this will make sure that they are
        // and stored in cache. Since offsetGet looks at cache first, items already
        // instantiated will be skipped.
        foreach($this->items as $key => $item) {
            $this->offsetGet($key);
        }

        // Now we can walk the cache array and retrieve any class objects returned by
        // the closures that were passed to the container
        foreach($this->cache as $offset => $item) {
            if(is_object($item)) {
                $callables[$offset] = $item;
            }
        }

        return $callables;
    }
}