<?php
/**
 * Define the posts management functionality.
 *
 * Loads and defines the posts management files for this plugin so that it is ready for post creation, edition or removal.
 *  
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Functions_Post {
	/**
	 * Insert a new post into the database
	 * 
	 * @param string $title
	 * @param string $content
	 * @param string $excerpt
	 * @param string $name
	 * @param string $type
	 * @param string $status
	 * @param int $author
	 * @param int $parent
	 * @param array $cats
	 * @param array $tags
	 * @param array $postmeta
	 * @param bool $overwrite_id Overwrites the post if it already exists checking existing post by post name
	 * 
	 * @since    1.0.0
	 */
	public function mailpn_insert_post($title, $content, $excerpt, $name, $type, $status, $author = 1, $parent = 0, $cats = [], $tags = [], $postmeta = [], $overwrite_id = true) {
    $post_values = [
      'post_title' => trim($title),
      'post_content' => $content,
      'post_excerpt' => $excerpt,
      'post_name' => $name,
      'post_type' => $type,
      'post_status' => $status,
      'post_author' => $author,
      'post_parent' => $parent,
      'comment_status' => 'closed',
      'ping_status' => 'closed',
    ];

    if (!is_admin()) {
      require_once(ABSPATH . 'wp-admin/includes/post.php');
    }

    if (!post_exists($title, '', '', $type) || !$overwrite_id) {
      $post_id = wp_insert_post($post_values);
    }else{
      $posts = get_posts(['fields' => 'ids', 'post_type' => $type, 'title' => $title, 'post_status' => 'any', ]);
      $post_id = !empty($posts) ? $posts[0] : 0;

      if (!empty($post_id)) {
        wp_update_post(['ID' => $post_id, 'post_title' => $title, 'post_content' => $content, 'post_excerpt' => $excerpt, 'post_name' => $name, 'post_type' => $type, 'post_status' => $status, ]);
      }else{
        return false;
      }
    }

    if (!empty($cats)) {
      wp_set_post_categories($post_id, $cats);
      if ($type == 'product') {
        wp_set_post_terms($post_id, $cats, 'product_cat', true);
      }
    }

    if (!empty($tags)) {
      wp_set_post_tags($post_id, $tags);
      if ($type == 'product') {
        wp_set_post_terms($post_id, $tags, 'product_tag', true);
      }
    }
 
    if (!empty($postmeta)) {
      foreach ($postmeta as $meta_key => $meta_value) {
        if ((is_array($meta_value) && count($meta_value)) || (!is_array($meta_value) && (!empty($meta_value) || (string)($meta_value) == '0'))) {
          update_post_meta($post_id, $meta_key, $meta_value);
        }
      }
    }

    return $post_id;
  }

  public function mailpn_duplicate_post($post_id, $post_status = 'draft') {
    global $wpdb;
    $post = get_post($post_id);
    $new_post_author = 1;

    if (isset($post) && $post != null) {
      $args = [
        'comment_status' => $post->comment_status,
        'ping_status'    => $post->ping_status,
        'post_author'    => $new_post_author,
        'post_content'   => $post->post_content,
        'post_excerpt'   => $post->post_excerpt,
        'post_name'      => $post->post_name,
        'post_parent'    => $post->post_parent,
        'post_password'  => $post->post_password,
        'post_status'    => $post_status,
        'post_title'     => $post->post_title,
        'post_type'      => $post->post_type,
        'to_ping'        => $post->to_ping,
        'menu_order'     => $post->menu_order
      ];
   
      $new_post_id = wp_insert_post($args);

      $taxonomies = get_object_taxonomies($post->post_type);
      foreach ($taxonomies as $taxonomy) {
        $post_terms = wp_get_object_terms($post_id, $taxonomy, ['fields' => 'slugs']);
        wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
      }
   
      $post_meta_infos = $wpdb->get_results($wpdb->prepare("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", $post_id));
      if (count($post_meta_infos) != 0) {
        foreach ($post_meta_infos as $meta_info) {
          $meta_key = $meta_info->meta_key;

          if($meta_key == '_wp_old_slug') {
            continue;
          }

          $wpdb->insert(
            $wpdb->postmeta,
            array(
              'post_id' => $new_post_id,
              'meta_key' => $meta_key,
              'meta_value' => $meta_info->meta_value
            ),
            array('%d', '%s', '%s')
          );
        }
      }

      return $new_post_id;
    }else{
      wp_die(esc_html(__('Post creation failed, could not find original post:', 'mailpn')) . ' ' . esc_html($post_id));
    }
  }
}