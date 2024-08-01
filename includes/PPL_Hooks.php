<?php
class PPL_Hooks {
    public static function add_admin_menu() {
        $capability = current_user_can('ppl_seo_user') ? 'access_published_posts_list' : 'manage_options';
        add_menu_page(
            'Published Posts List',  // Page title
            'Posts List',            // Menu title
            $capability,             // Capability
            'published-posts-list',  // Menu slug
            ['PPL_Admin_Page', 'display_admin_page'] // Function to display the page
        );

        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_scripts']);
    }

    public static function enqueue_admin_scripts($hook_suffix) {
        // Only enqueue scripts and styles on the specific admin page
        if ($hook_suffix === 'toplevel_page_published-posts-list') {
            wp_enqueue_script('ppl-admin-js', PPL_PLUGIN_URL . 'assets/js/admin.js', ['jquery'], null, true);
            wp_localize_script('ppl-admin-js', 'pplAdmin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ppl_remove_links_nonce')
            ));
            wp_enqueue_style('ppl-admin-css', PPL_PLUGIN_URL . 'assets/css/admin.css', [], null);
        }
    }

    public static function display_admin_page() {
        include PPL_PLUGIN_PATH . 'templates/admin_page.php';
    }

    public static function handle_txt_export() {
        if (isset($_POST['ppl_export_txt'])) {
            check_admin_referer('ppl_export_txt_action', 'ppl_export_txt_nonce');

            // Query to get all published posts
            $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => -1
            );
            $published_posts = new WP_Query($args);

            // Prepare an array to hold the post data
            $posts_data = array();

            // Loop through each post
            if ($published_posts->have_posts()) {
                while ($published_posts->have_posts()) {
                    $published_posts->the_post();
                    $link_count = PPL_Functions::count_outgoing_links(get_the_ID());
                    if ($link_count > 0) {
                        $posts_data[] = esc_url(get_permalink());
                    }
                }
            }

            // Reset post data
            wp_reset_postdata();

            // Create the text content
            $txt_content = "posts = [\n";
            foreach ($posts_data as $permalink) {
                $txt_content .= "    '" . str_replace("'", "\\'", $permalink) . "',\n";
            }
            $txt_content .= "]";

            // Set the TXT headers and output the TXT data
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="published-posts.txt"');
            echo $txt_content;
            exit;
        }
    }

    public static function remove_selected_links_from_post() {
        // Check nonce for security
        check_ajax_referer('ppl_remove_links_nonce', 'nonce');
    
        $post_id = intval($_POST['post_id']);
        $links_to_remove = isset($_POST['links']) ? array_map('esc_url_raw', $_POST['links']) : [];
    
        if ($post_id && !empty($links_to_remove)) {
            $post_content = get_post_field('post_content', $post_id);
            $dom = new DOMDocument;
            libxml_use_internal_errors(true);
            $dom->loadHTML(mb_encode_numericentity($post_content, [0x80, 0xffff, 0, 0xffff], 'UTF-8'));
            libxml_clear_errors();
            $links = $dom->getElementsByTagName('a');
    
            // Collect all links in an array
            $links_array = [];
            foreach ($links as $link) {
                $href = $link->getAttribute('href');
                if (strpos($href, 'epicflow.com') === false) {
                    $links_array[] = $link;
                }
            }
    
            // Remove the selected links
            foreach ($links_to_remove as $link_to_remove) {
                foreach ($links_array as $link) {
                    if ($link->getAttribute('href') == $link_to_remove) {
                        $link_text = $link->nodeValue;
                        $link->parentNode->replaceChild($dom->createTextNode($link_text), $link);
                        break; // Exit the inner loop once the link is found and replaced
                    }
                }
            }
    
            $new_content = $dom->saveHTML($dom->documentElement);
            $new_content = str_replace('<?xml encoding="UTF-8">', '', $new_content); // Remove the encoding declaration
            wp_update_post([
                'ID' => $post_id,
                'post_content' => wp_kses_post($new_content)
            ]);
    
            wp_send_json_success();
        } else {
            wp_send_json_error('Invalid post ID or no links selected');
        }
    }

    public static function add_custom_body_class($classes) {
        $screen = get_current_screen();
        if ($screen->id == 'toplevel_page_published-posts-list') {
            $classes .= ' ppl_admin_page';
        }
        return $classes;
    }
}

// Register the AJAX handler
add_action('wp_ajax_ppl_remove_selected_links', ['PPL_Hooks', 'remove_selected_links_from_post']);

// Add custom body class
add_filter('admin_body_class', ['PPL_Hooks', 'add_custom_body_class']);
