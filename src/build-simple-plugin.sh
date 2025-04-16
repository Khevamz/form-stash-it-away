
#!/bin/bash

echo "Building simple Form Stash WordPress plugin..."

# Create plugin directory
mkdir -p form-stash-simple

# Copy the main plugin file
cp form-stash.php form-stash-simple/

# Create a simple readme file
cat > form-stash-simple/readme.txt << EOL
=== Form Stash Simple ===
Contributors: formstash
Tags: forms, contact form
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 0.1.0
License: GPLv2 or later

A simplified form plugin for WordPress - testing version.

== Description ==

Form Stash Simple is a lightweight form plugin for WordPress.

== Installation ==

1. Upload the plugin files to the \`/wp-content/plugins/form-stash-simple\` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the shortcode [form_stash_simple] to display the form on any post or page
EOL

# Create the zip file
echo "Creating zip file..."
zip -r form-stash-simple.zip form-stash-simple

echo "Plugin build complete! form-stash-simple.zip is ready for upload to WordPress."
