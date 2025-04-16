
<?php
/**
 * The core plugin class
 */
class Form_Stash {
    /**
     * Initialize the plugin and define hooks
     */
    public function run() {
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_shortcode('form_stash', array($this, 'render_form_shortcode'));
        
        // Register REST API endpoints
        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }
    
    /**
     * Add menu items to WordPress admin
     */
    public function add_admin_menu() {
        add_menu_page(
            'Form Stash',
            'Form Stash',
            'manage_options',
            'form-stash',
            array($this, 'render_admin_page'),
            'dashicons-feedback',
            30
        );
    }
    
    /**
     * Enqueue assets for admin pages
     */
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_form-stash' !== $hook) {
            return;
        }
        
        // Enqueue React app
        wp_enqueue_style(
            'form-stash-admin-styles',
            FORM_STASH_PLUGIN_URL . 'admin/css/app.css',
            array(),
            FORM_STASH_VERSION
        );
        
        wp_enqueue_script(
            'form-stash-admin-script',
            FORM_STASH_PLUGIN_URL . 'admin/js/app.js',
            array('wp-element'),
            FORM_STASH_VERSION,
            true
        );
        
        // Localize script with WP data
        wp_localize_script('form-stash-admin-script', 'formStashData', array(
            'apiUrl' => rest_url('form-stash/v1'),
            'nonce' => wp_create_nonce('wp_rest'),
        ));
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_register_style(
            'form-stash-frontend-styles',
            FORM_STASH_PLUGIN_URL . 'public/css/app.css',
            array(),
            FORM_STASH_VERSION
        );
        
        wp_register_script(
            'form-stash-frontend-script',
            FORM_STASH_PLUGIN_URL . 'public/js/app.js',
            array('wp-element'),
            FORM_STASH_VERSION,
            true
        );
    }
    
    /**
     * Render React container for admin page
     */
    public function render_admin_page() {
        echo '<div id="form-stash-admin" class="wrap"></div>';
    }
    
    /**
     * Render form via shortcode
     */
    public function render_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts, 'form_stash');
        
        if (empty($atts['id'])) {
            return '<p>Error: Form ID is required</p>';
        }
        
        // Enqueue frontend assets
        wp_enqueue_style('form-stash-frontend-styles');
        wp_enqueue_script('form-stash-frontend-script');
        
        // Localize script with form data
        wp_localize_script('form-stash-frontend-script', 'formStashData', array(
            'apiUrl' => rest_url('form-stash/v1'),
            'nonce' => wp_create_nonce('wp_rest'),
            'formId' => $atts['id']
        ));
        
        // Container for React form
        return '<div id="form-stash-form-' . esc_attr($atts['id']) . '" class="form-stash-container"></div>';
    }
    
    /**
     * Register REST API endpoints
     */
    public function register_rest_routes() {
        // Get all forms
        register_rest_route('form-stash/v1', '/forms', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_forms'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ));
        
        // Get single form
        register_rest_route('form-stash/v1', '/forms/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_form'),
            'permission_callback' => '__return_true', // Public read access
        ));
        
        // Create form
        register_rest_route('form-stash/v1', '/forms', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_form'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ));
        
        // Update form
        register_rest_route('form-stash/v1', '/forms/(?P<id>\d+)', array(
            'methods' => 'PUT',
            'callback' => array($this, 'update_form'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ));
        
        // Delete form
        register_rest_route('form-stash/v1', '/forms/(?P<id>\d+)', array(
            'methods' => 'DELETE',
            'callback' => array($this, 'delete_form'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ));
        
        // Submit form
        register_rest_route('form-stash/v1', '/submit', array(
            'methods' => 'POST',
            'callback' => array($this, 'submit_form'),
            'permission_callback' => '__return_true', // Public submit access
        ));
        
        // Get submissions
        register_rest_route('form-stash/v1', '/submissions', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_submissions'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ));
        
        // Get submission
        register_rest_route('form-stash/v1', '/submissions/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_submission'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ));
        
        // Delete submission
        register_rest_route('form-stash/v1', '/submissions/(?P<id>\d+)', array(
            'methods' => 'DELETE',
            'callback' => array($this, 'delete_submission'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ));
    }
    
    /**
     * Get all forms
     */
    public function get_forms() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'form_stash_forms';
        $forms = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
        
        return rest_ensure_response($forms);
    }
    
    /**
     * Get single form
     */
    public function get_form($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'form_stash_forms';
        $form_id = $request['id'];
        
        $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $form_id));
        
        if (!$form) {
            return new WP_Error('not_found', 'Form not found', array('status' => 404));
        }
        
        return rest_ensure_response($form);
    }
    
    /**
     * Create form
     */
    public function create_form($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'form_stash_forms';
        $params = $request->get_json_params();
        
        $name = sanitize_text_field($params['name']);
        $fields = wp_json_encode($params['fields']);
        $success_message = sanitize_text_field($params['successMessage']);
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'fields' => $fields,
                'success_message' => $success_message,
            ),
            array('%s', '%s', '%s')
        );
        
        if (!$result) {
            return new WP_Error('db_error', 'Could not create form', array('status' => 500));
        }
        
        $form_id = $wpdb->insert_id;
        $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $form_id));
        
        return rest_ensure_response($form);
    }
    
    /**
     * Update form
     */
    public function update_form($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'form_stash_forms';
        $form_id = $request['id'];
        $params = $request->get_json_params();
        
        $name = sanitize_text_field($params['name']);
        $fields = wp_json_encode($params['fields']);
        $success_message = sanitize_text_field($params['successMessage']);
        
        $result = $wpdb->update(
            $table_name,
            array(
                'name' => $name,
                'fields' => $fields,
                'success_message' => $success_message,
            ),
            array('id' => $form_id),
            array('%s', '%s', '%s'),
            array('%d')
        );
        
        if ($result === false) {
            return new WP_Error('db_error', 'Could not update form', array('status' => 500));
        }
        
        $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $form_id));
        
        return rest_ensure_response($form);
    }
    
    /**
     * Delete form
     */
    public function delete_form($request) {
        global $wpdb;
        $forms_table = $wpdb->prefix . 'form_stash_forms';
        $submissions_table = $wpdb->prefix . 'form_stash_submissions';
        $form_id = $request['id'];
        
        // Delete form
        $result = $wpdb->delete(
            $forms_table,
            array('id' => $form_id),
            array('%d')
        );
        
        if (!$result) {
            return new WP_Error('db_error', 'Could not delete form', array('status' => 500));
        }
        
        // Delete associated submissions
        $wpdb->delete(
            $submissions_table,
            array('form_id' => $form_id),
            array('%d')
        );
        
        return rest_ensure_response(array(
            'deleted' => true,
            'id' => $form_id
        ));
    }
    
    /**
     * Submit form
     */
    public function submit_form($request) {
        global $wpdb;
        $forms_table = $wpdb->prefix . 'form_stash_forms';
        $submissions_table = $wpdb->prefix . 'form_stash_submissions';
        $params = $request->get_json_params();
        
        $form_id = absint($params['formId']);
        $form_data = $params['data'];
        
        // Verify form exists
        $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM $forms_table WHERE id = %d", $form_id));
        
        if (!$form) {
            return new WP_Error('not_found', 'Form not found', array('status' => 404));
        }
        
        // Insert submission
        $result = $wpdb->insert(
            $submissions_table,
            array(
                'form_id' => $form_id,
                'data' => wp_json_encode($form_data),
            ),
            array('%d', '%s')
        );
        
        if (!$result) {
            return new WP_Error('db_error', 'Could not save submission', array('status' => 500));
        }
        
        $submission_id = $wpdb->insert_id;
        
        return rest_ensure_response(array(
            'id' => $submission_id,
            'message' => $form->success_message
        ));
    }
    
    /**
     * Get submissions
     */
    public function get_submissions($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'form_stash_submissions';
        $form_id = isset($request['form_id']) ? absint($request['form_id']) : 0;
        
        $query = "SELECT * FROM $table_name";
        if ($form_id > 0) {
            $query .= $wpdb->prepare(" WHERE form_id = %d", $form_id);
        }
        $query .= " ORDER BY id DESC";
        
        $submissions = $wpdb->get_results($query);
        
        foreach ($submissions as &$submission) {
            $submission->data = json_decode($submission->data);
        }
        
        return rest_ensure_response($submissions);
    }
    
    /**
     * Get single submission
     */
    public function get_submission($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'form_stash_submissions';
        $submission_id = $request['id'];
        
        $submission = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $submission_id));
        
        if (!$submission) {
            return new WP_Error('not_found', 'Submission not found', array('status' => 404));
        }
        
        $submission->data = json_decode($submission->data);
        
        return rest_ensure_response($submission);
    }
    
    /**
     * Delete submission
     */
    public function delete_submission($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'form_stash_submissions';
        $submission_id = $request['id'];
        
        $result = $wpdb->delete(
            $table_name,
            array('id' => $submission_id),
            array('%d')
        );
        
        if (!$result) {
            return new WP_Error('db_error', 'Could not delete submission', array('status' => 500));
        }
        
        return rest_ensure_response(array(
            'deleted' => true,
            'id' => $submission_id
        ));
    }
}
