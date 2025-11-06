#!/usr/bin/env python3
"""
Script to update all admin files to use centralized config and sidebar components.
This eliminates code duplication across ~30 files.
"""

import re
import os
from pathlib import Path

def calculate_env_path(file_path):
    """Calculate the relative path to .env based on file location."""
    depth = len(Path(file_path).relative_to('/home/user/CF_GYM').parts) - 1
    return '../' * depth + '.env'

def calculate_sidebar_path(file_path):
    """Calculate the relative path to includes directory."""
    depth = len(Path(file_path).relative_to('/home/user/CF_GYM').parts) - 1
    return '../' * depth + 'includes/admin_sidebar.php'

def determine_current_page(file_path):
    """Determine the current_page value based on file path."""
    path = Path(file_path)
    parts = path.parts

    # Map file paths to page identifiers
    if 'dashboard' in parts:
        return 'dashboard'
    elif 'users' in parts:
        return 'users'
    elif 'statistics' in parts:
        return 'statistics'
    elif 'sell' in parts:
        return 'sell'
    elif 'invoices' in parts:
        return 'invoices'
    elif 'mainsettings' in parts:
        return 'mainsettings'
    elif 'workers' in parts:
        return 'workers'
    elif 'packages' in parts:
        return 'packages'
    elif 'hours' in parts:
        return 'hours'
    elif 'smtp' in parts:
        return 'smtp'
    elif 'chroom' in parts:
        return 'chroom'
    elif 'rule' in parts:
        return 'rule'
    elif 'tickets' in parts:
        return 'tickets'
    elif 'timetable' in parts:
        return 'timetable'
    elif 'trainers' in parts:
        return 'trainers'
    elif 'updater' in parts:
        return 'updater'
    elif 'log' in parts:
        return 'log'
    else:
        return 'dashboard'

def update_admin_file(file_path):
    """Update a single admin file to use centralized components."""
    print(f"Processing: {file_path}")

    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content

    # Pattern 1: Replace the entire config section
    # This includes session_start, userid setting, read_env_file function, and all config loading
    config_pattern = r'<\?php\s*session_start\(\);.*?if \(\$conn->connect_error\) \{[^}]+\}'

    env_path = calculate_env_path(file_path)
    sidebar_path = calculate_sidebar_path(file_path)

    config_replacement = '''<?php
require_once __DIR__ . '/''' + sidebar_path + '''/../config.php';

if (!isset($_SESSION['adminuser'])) {
    header("Location: ../");
    exit();
}

$userid = $_SESSION['adminuser'];'''

    content = re.sub(config_pattern, config_replacement, content, flags=re.DOTALL)

    # Pattern 2: Replace version check code
    version_check_pattern = r'(//\s*API!.*?)?(\$file_path\s*=\s*[\'"]https://api\.gymoneglobal\.com/latest/version\.txt[\'"];.*?\$is_new_version_available\s*=\s*version_compare\([^;]+\);)'
    version_check_replacement = r'// Check for available updates\n$is_new_version_available = check_for_updates($version);'

    content = re.sub(version_check_pattern, version_check_replacement, content, flags=re.DOTALL)

    # Pattern 3: Replace sidebar HTML
    # Find the sidebar div and replace it with include
    sidebar_pattern = r'<div class="col-sm-2 sidenav[^>]*>.*?</div>\s*(?=<br>|<div class="col-sm-10">)'

    current_page = determine_current_page(file_path)
    sidebar_replacement = f'''<?php
            $current_page = '{current_page}';
            require_once __DIR__ . '/{sidebar_path}';
            ?>'''

    content = re.sub(sidebar_pattern, sidebar_replacement, content, flags=re.DOTALL)

    # Only write if content changed
    if content != original_content:
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"  ✓ Updated: {file_path}")
        return True
    else:
        print(f"  - No changes needed: {file_path}")
        return False

def main():
    """Main function to process all admin files."""
    admin_dir = Path('/home/user/CF_GYM/admin')

    # Find all index.php files in admin directory
    admin_files = list(admin_dir.rglob('index.php'))

    print(f"Found {len(admin_files)} admin files to process\n")

    updated_count = 0
    for file_path in admin_files:
        if update_admin_file(str(file_path)):
            updated_count += 1

    print(f"\n✓ Complete! Updated {updated_count} out of {len(admin_files)} files.")

if __name__ == '__main__':
    main()
