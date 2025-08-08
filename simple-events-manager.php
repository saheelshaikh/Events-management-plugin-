<?php
/**
 * Plugin Name: Simple Events Manager
 * Description: Manage events (create, view, edit, delete) and display them with a shortcode.
 * Version: 1.0
 * Author: Mohammed Saheel Shaikh
 */

// Security check
if (!defined('ABSPATH')) exit;

// Register custom post type on init
function sem_register_event_post_type() {
    register_post_type('sem_event', [
        'labels' => [
            'name' => 'Events',
            'singular_name' => 'Event',
            'add_new_item' => 'Add New Event',
            'edit_item' => 'Edit Event',
            'new_item' => 'New Event',
            'view_item' => 'View Event',
            'search_items' => 'Search Events',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-calendar',
        'supports' => ['title', 'editor'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'sem_register_event_post_type');

// Add meta box
function sem_add_event_meta_boxes() {
    add_meta_box('sem_event_details', 'Event Details', 'sem_event_meta_box_html', 'sem_event');
}
add_action('add_meta_boxes', 'sem_add_event_meta_boxes');

// HTML for the fields
function sem_event_meta_box_html($post) {
    $date = get_post_meta($post->ID, '_sem_event_date', true);
   $location = get_post_meta($post->ID, '_sem_event_location', true);
$organizer = get_post_meta($post->ID, '_sem_event_organizer', true);

// ✅ Use default settings if empty
if (empty($location)) {
    $location = get_option('sem_default_location', '');
}
if (empty($organizer)) {
    $organizer = get_option('sem_default_organizer', '');
}

    ?>
    <p><label>Date:</label><br><input type="date" name="sem_event_date" value="<?= esc_attr($date) ?>"></p>
    <p><label>Location:</label><br><input type="text" name="sem_event_location" value="<?= esc_attr($location) ?>"></p>
    <p><label>Organizer:</label><br><input type="text" name="sem_event_organizer" value="<?= esc_attr($organizer) ?>"></p>
    <?php
}

// Save data
function sem_save_event_meta($post_id) {
    if (array_key_exists('sem_event_date', $_POST)) {
        update_post_meta($post_id, '_sem_event_date', sanitize_text_field($_POST['sem_event_date']));
        update_post_meta($post_id, '_sem_event_location', sanitize_text_field($_POST['sem_event_location']));
        update_post_meta($post_id, '_sem_event_organizer', sanitize_text_field($_POST['sem_event_organizer']));
    }
}
add_action('save_post', 'sem_save_event_meta');

// Shortcode to display events
function sem_event_shortcode() {
    $args = [
        'post_type' => 'sem_event',
        'posts_per_page' => 5,
        'orderby' => 'meta_value',
        'meta_key' => '_sem_event_date',
        'order' => 'ASC',
        'meta_query' => [[
            'key' => '_sem_event_date',
            'value' => date('Y-m-d'),
            'compare' => '>=',
            'type' => 'DATE'
        ]]
    ];
    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        echo '<ul>';
        while ($query->have_posts()) {
            $query->the_post();
            $date = get_post_meta(get_the_ID(), '_sem_event_date', true);
            $location = get_post_meta(get_the_ID(), '_sem_event_location', true);
            $organizer = get_post_meta(get_the_ID(), '_sem_event_organizer', true);
            echo '<li>';
            echo '<h3>' . get_the_title() . '</h3>';
            echo '<p>' . get_the_excerpt() . '</p>';
            echo '<p><strong>Date:</strong> ' . esc_html($date) . '</p>';
            echo '<p><strong>Location:</strong> ' . esc_html($location) . '</p>';
            echo '<p><strong>Organizer:</strong> ' . esc_html($organizer) . '</p>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No upcoming events.</p>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('events_list', 'sem_event_shortcode');

// ✅ Add settings menu item under "Settings"
function sem_add_settings_menu() {
    add_options_page(
        'Simple Events Settings',     // Page title
        'Events Settings',            // Menu title in sidebar
        'manage_options',             // Capability
        'sem-settings',               // Menu slug
        'sem_settings_page_html'      // Callback function to display the page
    );
}
add_action('admin_menu', 'sem_add_settings_menu');

// ✅ Register plugin settings
function sem_register_settings() {
    register_setting('sem_settings_group', 'sem_default_organizer');
    register_setting('sem_settings_group', 'sem_default_location');

    add_settings_section(
        'sem_main_settings',
        'Default Event Settings',
        null,
        'sem-settings'
    );

    add_settings_field(
        'sem_default_organizer',
        'Default Organizer',
        'sem_default_organizer_field_html',
        'sem-settings',
        'sem_main_settings'
    );

    add_settings_field(
        'sem_default_location',
        'Default Location',
        'sem_default_location_field_html',
        'sem-settings',
        'sem_main_settings'
    );
}
add_action('admin_init', 'sem_register_settings');

// ✅ Input fields for settings
function sem_default_organizer_field_html() {
    $value = get_option('sem_default_organizer', '');
    echo '<input type="text" name="sem_default_organizer" value="' . esc_attr($value) . '" />';
}

function sem_default_location_field_html() {
    $value = get_option('sem_default_location', '');
    echo '<input type="text" name="sem_default_location" value="' . esc_attr($value) . '" />';
}

// ✅ HTML output for the settings page
function sem_settings_page_html() {
    ?>
    <div class="wrap">
        <h1>Simple Events Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('sem_settings_group');
            do_settings_sections('sem-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
