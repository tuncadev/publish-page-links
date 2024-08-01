<?php

class PPL_Functions {
    public static function count_outgoing_links($post_id) {
        $post_content = get_post_field('post_content', $post_id);
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);

        $post_content = mb_encode_numericentity($post_content, [0x80, 0xffff, 0, 0xffff], 'UTF-8');
        $dom->loadHTML('<?xml encoding="UTF-8">' . $post_content);
        
        // Retrieve and log the errors
        $post_link = get_the_permalink($post_id);
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            $line = $error->line;
            $column = $error->column;
            $message = trim($error->message);
            
            // Log the error
            error_log("Libxml error on page: {$post_link} at line {$line}, column {$column}");
            
            // Retrieve the specific line
            $content_lines = explode("\n", $post_content);
            if (isset($content_lines[$line - 1])) {
                $specific_line = $content_lines[$line - 1];
                // Display the specific line with an indicator for the column
                $highlighted_line = substr($specific_line, 0, $column - 1) . 
                                    ' [HERE] ' . 
                                    substr($specific_line, $column - 1);
                error_log("Line {$line}: " . $highlighted_line);
            }
        }
        libxml_clear_errors();

        $links = $dom->getElementsByTagName('a');
        $external_link_count = 0;
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if (filter_var($href, FILTER_VALIDATE_URL) && strpos($href, home_url()) === false && strpos($href, 'epicflow.com') === false) {
                $external_link_count++;
            }
        }
        return $external_link_count;
    }

    public static function get_outgoing_links($post_id) {
        $post_content = get_post_field('post_content', $post_id);
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);

        $post_content = mb_encode_numericentity($post_content, [0x80, 0xffff, 0, 0xffff], 'UTF-8');
        $dom->loadHTML('<?xml encoding="UTF-8">' . $post_content);
        libxml_clear_errors();

        $links = $dom->getElementsByTagName('a');
        $external_links = [];
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            $link_text = $link->nodeValue;
            if (filter_var($href, FILTER_VALIDATE_URL) && strpos($href, home_url()) === false && strpos($href, 'epicflow.com') === false) {
                $external_links[] = ['href' => esc_url($href), 'text' => esc_html($link_text)];
            }
        }
        return $external_links;
    }
}
