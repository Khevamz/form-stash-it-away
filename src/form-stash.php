
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

/**
 * Create submissions table on plugin activation
 */
function form_stash_activate() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'form_stash_submissions';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        message text NOT NULL,
        date_submitted datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'form_stash_activate');

/**
 * Add admin menu
 */
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
    
    add_submenu_page(
        'form-stash',
        'Submissions',
        'Submissions',
        'manage_options',
        'form-stash-submissions',
        'form_stash_submissions_page'
    );
}
add_action('admin_menu', 'form_stash_admin_menu');

/**
 * Admin main page content
 */
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

/**
 * Submissions page content
 */
function form_stash_submissions_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'form_stash_submissions';
    
    // Handle deletion
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $wpdb->delete($table_name, ['id' => $id], ['%d']);
        echo '<div class="notice notice-success is-dismissible"><p>Submission deleted successfully.</p></div>';
    }
    
    // Handle bulk actions
    if (isset($_POST['action']) && $_POST['action'] === 'delete_selected' && isset($_POST['submission_ids'])) {
        $ids = array_map('intval', $_POST['submission_ids']);
        foreach ($ids as $id) {
            $wpdb->delete($table_name, ['id' => $id], ['%d']);
        }
        echo '<div class="notice notice-success is-dismissible"><p>Selected submissions deleted successfully.</p></div>';
    }
    
    // Get submissions
    $submissions = $wpdb->get_results("SELECT * FROM $table_name ORDER BY date_submitted DESC");
    ?>
    <div class="wrap">
        <h1>Form Submissions</h1>
        
        <?php if (empty($submissions)) : ?>
            <div class="card">
                <p>No submissions found.</p>
            </div>
        <?php else : ?>
            <form method="post" action="">
                <div class="tablenav top">
                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
                        <select name="action" id="bulk-action-selector-top">
                            <option value="-1">Bulk Actions</option>
                            <option value="delete_selected">Delete</option>
                        </select>
                        <input type="submit" id="doaction" class="button action" value="Apply">
                    </div>
                    <br class="clear">
                </div>
                
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <td id="cb" class="manage-column column-cb check-column">
                                <input id="cb-select-all-1" type="checkbox">
                            </td>
                            <th scope="col" class="manage-column column-id">ID</th>
                            <th scope="col" class="manage-column column-name">Name</th>
                            <th scope="col" class="manage-column column-email">Email</th>
                            <th scope="col" class="manage-column column-message">Message</th>
                            <th scope="col" class="manage-column column-date">Date</th>
                            <th scope="col" class="manage-column column-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $submission) : ?>
                            <tr>
                                <th scope="row" class="check-column">
                                    <input type="checkbox" name="submission_ids[]" value="<?php echo $submission->id; ?>">
                                </th>
                                <td><?php echo $submission->id; ?></td>
                                <td><?php echo esc_html($submission->name); ?></td>
                                <td><?php echo esc_html($submission->email); ?></td>
                                <td><?php echo wp_trim_words(esc_html($submission->message), 10, '...'); ?></td>
                                <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($submission->date_submitted)); ?></td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=form-stash-submissions&action=view&id=' . $submission->id); ?>" class="button button-small">View</a>
                                    <a href="<?php echo admin_url('admin.php?page=form-stash-submissions&action=delete&id=' . $submission->id); ?>" class="button button-small" onclick="return confirm('Are you sure you want to delete this submission?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
            
            <?php
            // Handle detailed view
            if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $submission = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
                
                if ($submission) {
                    ?>
                    <div class="card" style="margin-top: 20px; max-width: 800px; padding: 20px;">
                        <h2>Submission Details</h2>
                        <p><strong>ID:</strong> <?php echo $submission->id; ?></p>
                        <p><strong>Name:</strong> <?php echo esc_html($submission->name); ?></p>
                        <p><strong>Email:</strong> <?php echo esc_html($submission->email); ?></p>
                        <p><strong>Date:</strong> <?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($submission->date_submitted)); ?></p>
                        <p><strong>Message:</strong></p>
                        <div style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
                            <?php echo nl2br(esc_html($submission->message)); ?>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
            
        <?php endif; ?>
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
        // Form processing
        if (isset($_POST['form_stash_submit'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'form_stash_submissions';
            
            $name = sanitize_text_field($_POST['name'] ?? '');
            $email = sanitize_email($_POST['email'] ?? '');
            $message = sanitize_textarea_field($_POST['message'] ?? '');
            
            // Save to database
            $wpdb->insert(
                $table_name,
                array(
                    'name' => $name,
                    'email' => $email,
                    'message' => $message
                ),
                array('%s', '%s', '%s')
            );
            
            echo '<div class="form-stash-success">Thank you for your submission!</div>';
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

// Plugin deactivation hook
function form_stash_deactivate($delete_data = false) {
    if ($delete_data) {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}form_stash_submissions");
    }
}
register_deactivation_hook(__FILE__, 'form_stash_deactivate');
