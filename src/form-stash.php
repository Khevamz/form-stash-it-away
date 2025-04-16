
<?php
/**
 * Plugin Name: Form Stash Simple
 * Description: A simple form management plugin for WordPress
 * Version: 0.1.0
 * Author: Form Stash
 * Text Domain: form-stash
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('FORM_STASH_VERSION', '0.1.0');
define('FORM_STASH_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FORM_STASH_PLUGIN_URL', plugin_dir_url(__FILE__));

// Add admin menu
function form_stash_admin_menu() {
    add_menu_page(
        'Form Stash',
        'Form Stash',
        'manage_options',
        'form-stash',
        'form_stash_admin_page',
        'dashicons-feedback',
        30
    );
}
add_action('admin_menu', 'form_stash_admin_menu');

// Admin page content
function form_stash_admin_page() {
    ?>
    <div class="wrap">
        <h1>Form Stash Simple</h1>
        <p>Welcome to Form Stash Simple! This is a basic version of the plugin.</p>
        
        <div class="card">
            <h2>Create a Test Form</h2>
            <p>This is a simple test form that you can embed using the shortcode below:</p>
            <code>[form_stash_simple]</code>
        </div>
    </div>
    <?php
}

// Register shortcode
function form_stash_shortcode() {
    ob_start();
    ?>
    <div class="form-stash-form">
        <h3>Contact Form</h3>
        <form method="post" action="">
            <p>
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required>
            </p>
            <p>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </p>
            <p>
                <label for="message">Message:</label>
                <textarea name="message" id="message" rows="5" required></textarea>
            </p>
            <p>
                <input type="submit" name="form_stash_submit" value="Submit">
            </p>
        </form>
        
        <?php
        // Simple form processing
        if (isset($_POST['form_stash_submit'])) {
            $name = sanitize_text_field($_POST['name'] ?? '');
            $email = sanitize_email($_POST['email'] ?? '');
            $message = sanitize_textarea_field($_POST['message'] ?? '');
            
            echo '<div class="form-stash-success">Thank you for your submission!</div>';
            
            // For debugging purposes
            echo '<!-- Form data received: ' . 
                esc_html($name) . ', ' . 
                esc_html($email) . ', ' . 
                esc_html(substr($message, 0, 20)) . '... -->';
        }
        ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('form_stash_simple', 'form_stash_shortcode');

// Add some basic styling
function form_stash_styles() {
    ?>
    <style>
        .form-stash-form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .form-stash-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-stash-form input[type="text"],
        .form-stash-form input[type="email"],
        .form-stash-form textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .form-stash-form input[type="submit"] {
            background: #0073aa;
            color: #fff;
            border: 0;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .form-stash-form input[type="submit"]:hover {
            background: #005177;
        }
        
        .form-stash-success {
            background: #dff0d8;
            color: #3c763d;
            padding: 10px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
    <?php
}
add_action('wp_head', 'form_stash_styles');
add_action('admin_head', 'form_stash_styles');

// Plugin activation hook
function form_stash_activate() {
    // Nothing to do for simple version
}
register_activation_hook(__FILE__, 'form_stash_activate');

// Plugin deactivation hook
function form_stash_deactivate() {
    // Nothing to do for simple version
}
register_deactivation_hook(__FILE__, 'form_stash_deactivate');
