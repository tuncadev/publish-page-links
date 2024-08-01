# Published Posts List Plugin

## Description

The Published Posts List plugin is a WordPress plugin that allows administrators to list all published posts along with their outbound links and permalinks. The plugin also provides functionality to export the list to TXT or XLSX formats. Additionally, it includes features to remove selected outbound links from posts and provides role-based access control for SEO users, limiting their access to only the plugin's functionality.

## Features

- List all published posts with outbound links and their permalinks.
- Export the list of posts to TXT or XLSX formats.
- Remove selected outbound links from posts.
- Role-based access control for SEO users.
  - SEO users can only access the Published Posts List plugin.
  - SEO users cannot remove outbound links.
  - SEO users do not have access to other WordPress admin functionalities.

## Installation

1. **Upload the Plugin:**
   - Download the plugin files and upload them to the `/wp-content/plugins/published-post-list` directory.

2. **Activate the Plugin:**
   - Activate the plugin through the 'Plugins' menu in WordPress.

3. **Add the SEO User Role:**
   - The plugin will automatically add a custom role `SEO User` with limited capabilities upon activation.

## Usage

### Admin Access

- Navigate to the "Posts List" menu under the WordPress admin dashboard.
- View the list of all published posts with their outbound links and permalinks.
- Export the list to TXT or XLSX formats.
- Remove selected outbound links (only available for users with admin privileges).

### SEO User Access

- Users with the `SEO User` role will only see the "Posts List" menu.
- SEO Users can view the list of all published posts with their outbound links and permalinks.
- SEO Users cannot remove outbound links.

## Customization

### Adding SEO User Role

The plugin automatically adds the `SEO User` role upon activation. The role has the following capabilities:
- `read` - Allow read access.
- `access_published_posts_list` - Custom capability for accessing the Published Posts List plugin.

### Removing Admin Menus for SEO Users

The plugin removes all other admin menus for users with the `SEO User` role to restrict their access to only the plugin.

### Removing Profile Access for SEO Users

The plugin removes access to the profile section for users with the `SEO User` role to further restrict their access.

## Development

### Folder Structure

published-post-list/
├── assets/
│ ├── css/
│ │ └── admin.css
│ └── js/
│ └── admin.js
├── classes/
│ └── PPL_Admin_Page.php
├── includes/
│ ├── PPL_Functions.php
│ └── PPL_Hooks.php
├── libs/
│ └── PhpSpreadsheet/
│ └── (all PhpSpreadsheet files)
├── templates/
│ └── admin_page.php
├── published-post-list.php
└── README.md


### Adding New Features

1. **Create a Branch:**
   - Create a new branch for your feature development.
     ```
     git checkout -b feature-name
     ```

2. **Make Your Changes:**
   - Add your feature implementation.

3. **Commit Your Changes:**
   - Commit your changes with a descriptive message.
     ```
     git commit -m "Add feature-name"
     ```

4. **Push to Repository:**
   - Push your branch to the repository.
     ```
     git push origin feature-name
     ```

5. **Create a Pull Request:**
   - Create a pull request to merge your changes into the main branch.

## Support

For support and issues, please contact [Ozgur Murat Tunca](mailto:tunca.development@gmail.com)

## License

This plugin is licensed under the GPL2 License. See the LICENSE file for more details.
