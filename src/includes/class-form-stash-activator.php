
<?php
/**
 * Fired during plugin activation
 */
class Form_Stash_Activator {
    /**
     * Create necessary database tables during activation
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Forms table
        $forms_table = $wpdb->prefix . 'form_stash_forms';
        $forms_sql = "CREATE TABLE IF NOT EXISTS $forms_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            fields longtext NOT NULL,
            success_message text NOT NULL,
            date_created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        // Submissions table
        $submissions_table = $wpdb->prefix . 'form_stash_submissions';
        $submissions_sql = "CREATE TABLE IF NOT EXISTS $submissions_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            form_id mediumint(9) NOT NULL,
            data longtext NOT NULL,
            date_submitted datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY form_id (form_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($forms_sql);
        dbDelta($submissions_sql);
        
        // Add version to options
        add_option('form_stash_db_version', FORM_STASH_VERSION);
    }
}
