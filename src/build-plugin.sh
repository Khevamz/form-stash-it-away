
#!/bin/bash

# Create plugin directory
mkdir -p form-stash

# Copy PHP files
cp form-stash.php form-stash/
mkdir -p form-stash/includes
cp includes/class-form-stash-activator.php form-stash/includes/
cp includes/class-form-stash-deactivator.php form-stash/includes/
cp includes/class-form-stash.php form-stash/includes/

# Create admin and public directories
mkdir -p form-stash/admin/js
mkdir -p form-stash/admin/css
mkdir -p form-stash/public/js
mkdir -p form-stash/public/css

# Copy base CSS files for public view
cp public/css/form-stash-public.css form-stash/public/css/app.css

# Build React frontend for admin
echo "Building admin React app..."
npx vite build --config vite.config.js --outDir form-stash/admin

# Build React frontend for public
echo "Building public React app..."
npx vite build --config vite.config.js --outDir form-stash/public

# Add readme.txt for WordPress plugin repository
cat > form-stash/readme.txt << EOL
=== Form Stash ===
Contributors: formstash
Tags: forms, submissions, contact form
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create and manage forms with ease. Store form submissions directly in your WordPress database.

== Description ==

Form Stash allows you to create custom forms and store submissions in your WordPress database.

Features:
* Create custom forms with various field types
* Manage form submissions
* Easy to use shortcode system
* Built with React for a smooth experience

== Installation ==

1. Upload the plugin files to the \`/wp-content/plugins/form-stash\` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to Form Stash in your admin menu to start creating forms

== Changelog ==

= 1.0.0 =
* Initial release
EOL

# Create the zip file
echo "Creating zip file..."
zip -r form-stash.zip form-stash

echo "Plugin build complete! form-stash.zip is ready for upload to WordPress."
