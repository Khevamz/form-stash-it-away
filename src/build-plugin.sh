
#!/bin/bash

# Create plugin directory
mkdir -p form-stash

# Copy PHP files
cp form-stash.php form-stash/
mkdir -p form-stash/includes
cp includes/class-form-stash-activator.php form-stash/includes/
cp includes/class-form-stash-deactivator.php form-stash/includes/
cp includes/class-form-stash.php form-stash/includes/

# Create admin directories
mkdir -p form-stash/admin/js
mkdir -p form-stash/admin/css
mkdir -p form-stash/public/js
mkdir -p form-stash/public/css

# Build React frontend for admin
echo "Building admin React app..."
npx vite build --outDir form-stash/admin

# Build React frontend for public
echo "Building public React app..."
npx vite build --outDir form-stash/public

# Create the zip file
echo "Creating zip file..."
zip -r form-stash.zip form-stash

echo "Plugin build complete! form-stash.zip is ready."
