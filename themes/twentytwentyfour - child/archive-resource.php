<?php get_header(); ?>
<!-- 
<form id="filter-form">
    <select name="taxonomy_term">
        <option value="">Select Resource Type or Topic</option>
        <?php
        // Get all taxonomies associated with the 'resource' post type
        // $taxonomies = get_object_taxonomies('resource', 'objects');

        // // Iterate through each taxonomy
        // foreach ($taxonomies as $taxonomy) {
        //     // Fetch all terms for the current taxonomy
        //     $terms = get_terms(array('taxonomy' => $taxonomy->name, 'hide_empty' => false));

        //     // Iterate through each term and create an option element
        //     foreach ($terms as $term) {
        //         echo '<option value="' . esc_attr($taxonomy->name . '_' . $term->term_id) . '" data-id="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . ' (' . esc_html($taxonomy->label) . ')</option>';
        //     }
        // }
        ?>
    </select>

    <input type="text" name="keyword" placeholder="Enter keyword">
</form>

<div id="resource-list">
     //Filtered resources will appear here 
</div> -->
<?php echo do_shortcode('[resource_filter]')?>

<?php get_footer();?>