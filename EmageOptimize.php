/*
Plugin Name: EmageOptimize 
Description: High-quality image resizing plugin with customization options, watermarking, and support for retina displays.
Version: 1.0
Author: Abdalla Mohamed
*/

// Enqueue scripts and styles
function emage_enqueue_scripts() {
    wp_enqueue_style('emage-admin-styles', plugins_url('css/emage-admin.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'emage_enqueue_scripts');

// Register the image sizes for retina displays
function emage_register_retina_image_sizes() {
    add_image_size('emage_retina_large', get_option('large_size_w') * 2, get_option('large_size_h') * 2, true);
    add_image_size('emage_retina_medium', get_option('medium_size_w') * 2, get_option('medium_size_h') * 2, true);
    add_image_size('emage_retina_thumbnail', get_option('thumbnail_size_w') * 2, get_option('thumbnail_size_h') * 2, true);
}
add_action('after_setup_theme', 'emage_register_retina_image_sizes');

// Override the image sizes based on user selection
function emage_override_image_sizes($sizes) {
    $emage_option = get_option('emage_option');
    if ($emage_option === 'maximum') {
        $sizes['large'] = array(
            'width' => get_option('large_size_w') * 2,
            'height' => get_option('large_size_h') * 2,
            'crop' => false,
        );
        $sizes['medium'] = array(
            'width' => get_option('medium_size_w') * 2,
            'height' => get_option('medium_size_h') * 2,
            'crop' => false,
        );
        $sizes['thumbnail'] = array(
            'width' => get_option('thumbnail_size_w') * 2,
            'height' => get_option('thumbnail_size_h') * 2,
            'crop' => false,
        );
    }
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'emage_override_image_sizes');

// Serve high-resolution images for retina displays
function emage_serve_retina_images($image_url, $attachment_id, $size) {
    $emage_option = get_option('emage_option');
    if ($emage_option === 'maximum' && function_exists('wp_get_attachment_image_src')) {
        $retina_image = wp_get_attachment_image_src($attachment_id, $size . '_retina');
        if ($retina_image && isset($retina_image[0])) {
            $image_url = $retina_image[0];
        }
    }
    return $image_url;
}
add_filter('wp_get_attachment_image_src', 'emage_serve_retina_images', 10, 3);

// Render the configuration page
function emage_render_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save settings
    if (isset($_POST['emage_save_settings'])) {
        // Perform validation and save settings here
        $emage_option = isset($_POST['emage_option']) ? sanitize_text_field($_POST['emage_option']) : '';
        $emage_format = isset($_POST['emage_format']) ? sanitize_text_field($_POST['emage_format']) : '';
        $emage_compression = isset($_POST['emage_compression']) ? absint($_POST['emage_compression']) : 
