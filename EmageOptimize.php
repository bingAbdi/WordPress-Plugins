<?php
    
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
        $emage_compression = isset($_POST['emage_compression']) ? absint($_POST['emage_compression']) : 90;

 // Default compression level
        $emage_aspect_ratio_lock = isset($_POST['emage_aspect_ratio_lock']) && $_POST['emage_aspect_ratio_lock'] === '1' ? 1 : 0;
        $emage_watermark_image = isset($_POST['emage_watermark_image']) ? sanitize_text_field($_POST['emage_watermark_image']) : '';
        $emage_watermark_position = isset($_POST['emage_watermark_position']) ? sanitize_text_field($_POST['emage_watermark_position']) : 'bottom-right';
        $emage_watermark_opacity = isset($_POST['emage_watermark_opacity']) ? absint($_POST['emage_watermark_opacity']) : 50; // Default opacity
        switch ($emage_option) {
            // Handle the resize options
        }
        update_option('emage_width_ratio', $width_ratio);
        update_option('emage_height_ratio', $height_ratio);
        update_option('emage_format', $emage_format);
        update_option('emage_compression', $emage_compression);
        update_option('emage_aspect_ratio_lock', $emage_aspect_ratio_lock);
        update_option('emage_watermark_image', $emage_watermark_image);
        update_option('emage_watermark_position', $emage_watermark_position);
        update_option('emage_watermark_opacity', $emage_watermark_opacity);
    }

    // Retrieve the saved settings
    $width_ratio = get_option('emage_width_ratio', 1.2);
    $height_ratio = get_option('emage_height_ratio', 1.1);
    $emage_format = get_option('emage_format', 'jpeg');
    $emage_compression = get_option('emage_compression', 90);
    $emage_aspect_ratio_lock = get_option('emage_aspect_ratio_lock', 0);
    $emage_watermark_image = get_option('emage_watermark_image', '');
    $emage_watermark_position = get_option('emage_watermark_position', 'bottom-right');
    $emage_watermark_opacity = get_option('emage_watermark_opacity', 50);

    // Render the settings page HTML

    <div class="wrap">
        <h1>Emage Settings</h1>
        <form method="post" action="" enctype="multipart/form-data">
            <table class="form-table">
                <tr>
                    <th scope="row">Image Resize Option</th>
                    <td>
                        <!-- Render resize options -->
                    </td>
                </tr>
                <tr>
                    <th scope="row">Image Format</th>
                    <td>
                        <select name="emage_format">
                            <option value="jpeg"<?php selected($emage_format, 'jpeg'); ?>>JPEG</option>
                            <option value="png"<?php selected($emage_format, 'png'); ?>>PNG</option>
                            <option value="gif"<?php selected($emage_format, 'gif'); ?>>GIF</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Compression Level</th>
                    <td>
                        <input type="range" name="emage_compression" min="1" max="100" value="<?php echo $emage_compression; ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Lock Aspect Ratio</th>
                    <td>
                        <label for="emage_aspect_ratio_lock">
                            <input type="checkbox" name="emage_aspect_ratio_lock" value="1"<?php checked($emage_aspect_ratio_lock, 1); ?>> Enable
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Watermark Image</th>
                    <td>
                        <input type="file" name="emage_watermark_image">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Watermark Position</th>
                    <td>
                        <select name="emage_watermark_position">
                            <option value="top-left"<?php selected($emage_watermark_position, 'top-left'); ?>>Top Left</option>
                            <option value="top-right"<?php selected($emage_watermark_position, 'top-right'); ?>>Top Right</option>
                            <option value="bottom-left"<?php selected($emage_watermark_position, 'bottom-left'); ?>>Bottom Left</option>
                            <option value="bottom-right"<?php selected($emage_watermark_position, 'bottom-right'); ?>>Bottom Right</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Watermark Opacity</th>
                    <td>
                        <input type="range" name="emage_watermark_opacity" min="0" max="100" value="<?php echo $emage_watermark_opacity; ?>">
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="emage_save_settings" class="button-primary" value="Save Settings">
            </p>
        </form>
    </div>
    <?php
}

// Add the configuration page to the admin menu
function emage_add_settings_page() {
    add_options_page('Emage Settings', 'Emage', 'manage_options', 'emage-settings', 'emage_render_settings_page');
}
add_action('admin_menu', 'emage_add_settings_page');

