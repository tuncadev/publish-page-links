<?php

class PPL_Admin_Page {
    public static function display_admin_page() {
        // Query to get all published posts
        $args = array(
          'post_type' => 'post',
          'post_status' => 'publish',
          'posts_per_page' => -1
      );
      $published_posts = new WP_Query($args);
      $posts_with_links = [];
      $total_posts_found = $published_posts->found_posts;
      $total_links = 0;
      // Loop through each post
      if ($published_posts->have_posts()) {
          while ($published_posts->have_posts()) {
              $published_posts->the_post();
              $link_count = PPL_Functions::count_outgoing_links(get_the_ID());
              if ($link_count > 0) {
                  $posts_with_links[] = [
                      'ID' => get_the_ID(),
                      'title' => get_the_title(),
                      'permalink' => get_permalink(),
                      'link_count' => $link_count,
                      'external_links' => PPL_Functions::get_outgoing_links(get_the_ID())
                  ];
                  $total_links += $link_count;
              }
              
          }
      }

      // Sort posts by link count in descending order
      usort($posts_with_links, function ($a, $b) {
          return $b['link_count'] - $a['link_count'];
      });
        ?>
        <div class="ppl_wrap">
          <div class="ppl_head_section">
            <h1>Published Posts With Outgoing Links</h1>
            <p>The plugin displays all published posts with outgoing links, with the exeption of inbound (internal) links.</p>
            <span class="ppl_warn">Please use with caution ! <strong>Deleting function are only for developers to execute.</strong></span>
          </div>
          <div class="ppl_admin_content">
          <div class="export_wrap">
              <form method="post" action="">
                  <?php wp_nonce_field('ppl_export_txt_action', 'ppl_export_txt_nonce'); ?>
                  <input type="submit" name="ppl_export_txt" class="button button-primary" value="Export to TXT">
              </form>
            </div>
          <div class="search_wrap">
                <input type="text" id="search-input" placeholder="Search titles...">
            </div>
            <div class="ppl_table_posts">
              <table class="ppl_posts_table widefat fixed" cellspacing="0" id="posts-table">
                <thead>
                    <tr class="sticky_tr">
                        <th id="column-title" class="manage-column column-title sortable" scope="col">Post Title (<?=$total_posts_found; ?>)</th>
                        <th id="column-permalink" class="manage-column column-permalink sortable" scope="col">Link to post</th>
                        <th id="column-links" class="manage-column column-links sortable" scope="col">Links (<?=$total_links;?>)</th>
                        <th id="column-actions" class="manage-column column-actions" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $i = 1;
                    // Display posts
                    foreach ($posts_with_links as $post) {
                        echo '<tr class="post-row">';
                        echo '<td>'.$i.') <a href="#" class="title-link" data-post-id="' . esc_attr($post['ID']) . '">' . esc_html($post['title']) . '</a></td>';
                        echo '<td><a href="' . esc_url($post['permalink']) . '" target="_blank">' . esc_url($post['permalink']) . '</a></td>';
                        echo '<td>' . esc_html($post['link_count']) . '</td>';
                        if (!current_user_can('ppl_seo_user')) {
                          echo '<td><a class="ppl_remove_button remove-selected-links" data-post-id="' . esc_attr($post['ID']) . '">Remove Selected Links</a></td>';
                        } else {
                            echo '<td>N/A</td>';
                        }
                      
                        echo '</tr>';
                        echo '<tr class="outgoing-links-row" id="links-row-' . esc_attr($post['ID']) . '" style="display:none;">';
                        echo '<td colspan="4">';
                        echo '<div class="outgoing-links"><strong>Outgoing Links:</strong><ul>';
                        foreach ($post['external_links'] as $link) {
                            echo '<li><label><input type="checkbox" value="' . esc_attr($link['href']) . '"> ' . esc_html($link['text']) . ' (<a href="' . esc_url($link['href']) . '" target="_blank">' . esc_url($link['href']) . '</a>)</label></li>';
                        }
                        echo '</ul></div>';
                        echo '</td>';
                        echo '</tr>';
                        $i += 1;
                    }

                    if (empty($posts_with_links)) {
                        echo '<tr><td colspan="4">No published posts found.</td></tr>';
                    }

                    // Reset post data
                    wp_reset_postdata();
                    ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <?php
    }
}
