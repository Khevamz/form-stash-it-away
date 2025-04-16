
<?php
/**
 * Plugin Name: Form Stash
 * Description: Create forms and store submissions easily
 * Version: 1.0.0
 * Author: Form Stash
 * Text Domain: form-stash
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('FORM_STASH_VERSION', '1.0.0');
define('FORM_STASH_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FORM_STASH_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once FORM_STASH_PLUGIN_DIR . 'includes/class-form-stash-activator.php';
require_once FORM_STASH_PLUGIN_DIR . 'includes/class-form-stash-deactivator.php';
require_once FORM_STASH_PLUGIN_DIR . 'includes/class-form-stash.php';

// Register activation and deactivation hooks
register_activation_hook(__FILE__, array('Form_Stash_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('Form_Stash_Deactivator', 'deactivate'));

// Initialize the plugin
function run_form_stash() {
    $plugin = new Form_Stash();
    $plugin->run();
}
run_form_stash();
