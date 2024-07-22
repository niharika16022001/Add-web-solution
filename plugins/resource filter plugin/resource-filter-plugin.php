<?php
/*
Plugin Name: Resource Filter
Description: Please check the readme file inside the plugin once for more detailed use of the plugin.
Version: 1.0
Author: Add Web Solution
*/

add_action('init', 'create_resource_post_type');
add_action('init', 'create_resource_taxonomies');
add_action('wp_enqueue_scripts', 'resource_filter_enqueue_scripts');

function create_resource_post_type() {
    register_post_type('resource',
        array(
            'labels' => array(
                'name' => __('Resources'),
                'singular_name' => __('Resource'),
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'resources'),
            'supports' => array('title', 'editor', 'thumbnail'),
            'menu_position' => 5,
            'menu_icon' => 'dashicons-archive',
        )
    );
}

function create_resource_taxonomies() {
    register_taxonomy('resource_type', 'resource', array(
        'label' => __('Resource Type'),
        'rewrite' => array('slug' => 'resource-type'),
        'hierarchical' => true,
    ));

    register_taxonomy('resource_topic', 'resource', array(
        'label' => __('Resource Topic'),
        'rewrite' => array('slug' => 'resource-topic'),
        'hierarchical' => true,
    ));
}

function resource_filter_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('resource-filter-ajax', plugin_dir_url(__FILE__) . 'js/resource-filter.js', array('jquery'), '1.0', true);
    wp_localize_script('resource-filter-ajax', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
}

add_action('wp_enqueue_scripts', 'resource_filter_enqueue_scripts');

function fetch_filtered_by_taxonomy() {

    global $wpdb;
    $taxonomy_term_id = isset($_POST['taxonomy_term_id']) ? intval($_POST['taxonomy_term_id']) : 0;

    $args = array(
        'post_type' => 'resource',
        'posts_per_page' => -1,
        'post_status' => 'publish', 
    );

    if ($taxonomy_term_id) {
        $term = get_term($taxonomy_term_id);

        if (!is_wp_error($term) && $term) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => $term->taxonomy,
                    'field' => 'term_id',
                    'terms' => $term->term_id,
                ),
            );
        } else {
            wp_send_json_error('Invalid taxonomy term');
            wp_die();
        }
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $response = '';
        while ($query->have_posts()) {
            $query->the_post();
            $response .= '<div class="resource-item">';
            $response .= '<h2>' . get_the_title() . '</h2>';
            $response .= '<div>' . get_the_excerpt() . '</div>';
            $response .= '</div>';
        }
        wp_reset_postdata();
        wp_send_json_success($response);
    } else {
        wp_send_json_success('<p>No resources found.</p>');
    }

    wp_die();
}
add_action('wp_ajax_fetch_filtered_by_taxonomy', 'fetch_filtered_by_taxonomy');
add_action('wp_ajax_nopriv_fetch_filtered_by_taxonomy', 'fetch_filtered_by_taxonomy');

function fetch_filtered_by_keyword() {

    global $wpdb;
    $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';
    $keyword_like = '%' . $wpdb->esc_like($keyword) . '%';

    $sql = "
        SELECT p.ID, p.post_title, p.post_content, p.post_excerpt
        FROM {$wpdb->posts} p
        WHERE p.post_type = 'resource'
        AND p.post_status = 'publish'
        AND (p.post_title LIKE %s OR p.post_content LIKE %s)
    ";

    $sql = $wpdb->prepare($sql, $keyword_like, $keyword_like);

    $results = $wpdb->get_results($sql);

    if ($results) {
        $response = '<ul>';
        foreach ($results as $post) {
            $post_url = get_permalink($post->ID);
            $post_excerpt = $post->post_excerpt ? $post->post_excerpt : wp_trim_words($post->post_content, 20); 

            $response .= '<li>';
            $response .= '<h2><a href="' . esc_url($post_url) . '">' . esc_html($post->post_title) . '</a></h2>';
            $response .= '<div>' . esc_html($post_excerpt) . '</div>';
            $response .= '<div>' . esc_html($post->post_content) . '</div>';
            $response .= '</li>';
        }
        $response .= '</ul>';

        wp_send_json_success($response);
    } else {
        wp_send_json_success('<p>No resources found.</p>');
    }

    wp_die();
}
add_action('wp_ajax_fetch_filtered_by_keyword', 'fetch_filtered_by_keyword');
add_action('wp_ajax_nopriv_fetch_filtered_by_keyword', 'fetch_filtered_by_keyword');

function resource_filter_shortcode() {
    ob_start();
    ?>
    <form id="filter-form">
        <select name="taxonomy_term">
            <option value="">Select Resource Type or Topic</option>
            <?php
            $taxonomies = get_object_taxonomies('resource', 'objects');
            foreach ($taxonomies as $taxonomy) {
                $terms = get_terms(array('taxonomy' => $taxonomy->name, 'hide_empty' => false));
                foreach ($terms as $term) {
                    echo '<option value="' . esc_attr($taxonomy->name . '_' . $term->term_id) . '" data-id="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . ' (' . esc_html($taxonomy->label) . ')</option>';
                }
            }
            ?>
        </select>
        <input type="text" name="keyword" placeholder="Enter keyword">
    </form>
    <div id="resource-list"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('resource_filter', 'resource_filter_shortcode');

register_activation_hook(__FILE__, 'create_resource_filter_page');
register_deactivation_hook(__FILE__, 'remove_resource_filter_page');

function create_resource_filter_page() {
    $page = array(
        'post_title'    => 'Resource Filter',
        'post_content'  => '[resource_filter]',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'page_template' => 'page-resource-filter.php'
    );
  
    $page_id = wp_insert_post($page);
  
    if ($page_id && !is_wp_error($page_id)) {
        update_option('resource_filter_page_id', $page_id);
    }
}

function remove_resource_filter_page() {
    $page_id = get_option('resource_filter_page_id');
    if ($page_id) {
        wp_delete_post($page_id, true);
        delete_option('resource_filter_page_id');
    }
}

