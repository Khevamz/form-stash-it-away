
<?php
/**
 * Fired during plugin deactivation
 */
class Form_Stash_Deactivator {
    /**
     * Plugin deactivation tasks
     * 
     * @param bool $delete_data Whether to delete all plugin data
     */
    public static function deactivate($delete_data = false) {
        // By default, we're not removing data on deactivation
        // This allows users to reactivate without losing their forms
        
        // If delete_data is true, remove all plugin data
        if ($delete_data) {
            global $wpdb;
            
            // Delete tables
            $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}form_stash_forms");
            $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}form_stash_submissions");
            
            // Delete options
            delete_option('form_stash_db_version');
        }
    }
}
