
#!/bin/bash

echo "Building Form Stash WordPress plugin..."

# Create directories
mkdir -p form-stash
mkdir -p form-stash/includes
mkdir -p form-stash/admin/js
mkdir -p form-stash/admin/css
mkdir -p form-stash/public/js
mkdir -p form-stash/public/css

# Copy PHP files
cp form-stash.php form-stash/
cp includes/* form-stash/includes/

# Copy README
cp README.txt form-stash/readme.txt

# Build React app
echo "Building React application..."
# In a real scenario, we would build the React app here
# For now, we'll just copy the CSS file
cp public/css/form-stash-public.css form-stash/public/css/

# Create placeholder JS files (in a real scenario, these would be built from the React app)
echo "// Form Stash Admin" > form-stash/admin/js/app.js
echo "// Form Stash Public" > form-stash/public/js/app.js

# Copy CSS files
cp public/css/form-stash-public.css form-stash/public/css/app.css
cp public/css/form-stash-public.css form-stash/admin/css/app.css

# Create zip file
echo "Creating ZIP archive..."
zip -r form-stash.zip form-stash

echo "Done! form-stash.zip is ready for upload."
