<?php

namespace AOD;

class Helper
{
    /**
     * @param null|\WP_Post $post
     *
     * @return int|mixed
     */
    public static function topmostParentId( $post = null ) {
        if( empty($post) || is_int( $post ) ) {
            $post = get_post( $post );
        }

        $ancestors = $post->ancestors;
        if ($ancestors) {
            return end($ancestors);
        }
        return $post->ID;
    }

    /**
     * Returns an array of numbered options to be used with an HTML select dropdown
     * @param int $start
     * @param int $end
     * @param int $times
     * @return array
     */
    public static function getNumberedOptions( $start = 1, $end = 20, $times = 1 ) {
        $pp = [];
        while($start <= $end) {
            $pp[$start] = $start;
            $start = $start + $times;
        }
        return $pp;
    }

    /**
     * Returns an array of categories to be used with an HTML select dropdown
     * @return array
     */
    public static function getCategoryOptions() {
        $output = [];
        $categories = get_categories(['hide_empty' => 0]);
        if($categories){
            foreach($categories as $category) {
                $output[$category->category_nicename] = "$category->cat_name [{$category->category_count}]";
            }
        }
        return $output;
    }

    /**
     * Returns an array of available posts to be used with an HTML select dropdown
     * @param string $post_type
     * @return array
     */
    public static function getPostOptions( $post_type = 'post' ) {
        $post_types = get_post_types();
        if(!in_array($post_type, $post_types)) {
            return ['-1' => $post_type . ' does not exist.'];
        }

        $q = new \WP_Query([
            'post_type' => $post_type,
            'posts_per_page' => -1,
        ]);
        $output = [];
        if($q->have_posts()) {
            while($q->have_posts()) {
                $q->the_post();
                $output[$q->post->ID] = $q->post->post_title;
            }
        }
        return $output;
    }

    /**
     * Combines two arrays
     * @param array $args
     * @param array $defaults
     * @return array
     */
    public static function parseArgs(&$args, $defaults)
    {
        $defaults = (array) $defaults;
        $result = (array) $args;
        foreach($defaults as $key => $defaultValue) {
            if(isset( $result[ $key ] )) {
                if ( is_array( $defaultValue ) ) {
                    $result[ $key ] = self::parseArgs( $result[ $key ],  $defaultValue);
                }
            } else {
                $result[ $key ] = $defaultValue;
            }
        }
        return $result;
    }

    /**
     * Formats a phone number from a string
     * Returns format like (000) 000-0000
     *
     * @param string|int $number
     * @return string
     */
    public static function formatPhoneNumber($number)
    {
        if(preg_match( '/^(\d{3})(\d{3})(\d{4})$/', preg_replace('/\D/', '', $number), $matches)) {
            return"({$matches[1]}) {$matches[2]}-{$matches[3]}";
        }
        return $number;
    }
}