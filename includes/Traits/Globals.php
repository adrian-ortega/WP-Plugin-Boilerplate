<?php

namespace AOD\Traits;

trait Globals
{
    /**
     * Returns the global WordPress database connector
     * @return \wpdb
     */
    public function db()
    {
        global $wpdb;
        return $wpdb;
    }

    /**
     * Returns the global Query
     * @return \WP_Query
     */
    public function query()
    {
        global $wp_query;
        return $wp_query;
    }

    /**
     * Returns the current screen
     * @return \WP_Screen
     */
    public function screen()
    {
        global $current_screen;
        return $current_screen;
    }

    /**
     * Returns the current User
     * @return \WP_User
     */
    protected function user() {
        global $current_user;
        return $current_user;
    }

    /**
     * Returns the current IP Address of of the user
     * @return mixed
     */
    protected function getIpAddress() {
        $ip = $_SERVER['REMOTE_ADDR'];

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $ip;
    }
}