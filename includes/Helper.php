<?php

namespace AOD;

use GcKit\Admin\Metaboxes\ExcludePage;

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

    public static function getExcludedPages() {
        $output = [];
        $q = new \WP_Query(array(
            'post_type' => 'page',
            'posts_per_page' => -1,
            'meta_key' => ExcludePage::METAKEY . '_exclude',
            'meta_query' => array(
                'key' => ExcludePage::METAKEY . '_exclude',
                'value' => '1',
                'compare' => '='
            ),
        ));
        if($q->have_posts()) {
            while($q->have_posts()){
                $q->the_post();
                $output[] = $q->post->ID;
            }
        }
        wp_reset_query();

        return $output;
    }

    public static function getNumberedOptions( $start = 1, $end = 20, $times = 1 ) {
        $pp = [];
        while($start <= $end) {
            $pp[$start] = $start;
            $start = $start + $times;
        }
        return $pp;
    }

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
}