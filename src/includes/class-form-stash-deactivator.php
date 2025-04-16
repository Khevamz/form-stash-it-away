
<?php
/**
 * Fired during plugin deactivation
 */
class Form_Stash_Deactivator {
    /**
     * Plugin deactivation tasks
     */
    public static function deactivate() {
        // For now, we're not removing any data on deactivation
        // This allows users to reactivate without losing their forms
    }
}
