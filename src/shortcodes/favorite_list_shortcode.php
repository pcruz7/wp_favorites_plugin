<?php

function favorite_list ($atts)
{
  $atts = shortcode_atts(array(
    'paged'          => 1,
    'posts_per_page' => 10,
    'order'          => 'ASC',
    'orderby'        => 'post__in',
    'ajax'           => false,
  ), $atts);

  $class      = "";
  $data_attr  = "";

  if (!is_user_logged_in() || (defined(WP_CACHE) && WP_CACHE)) {
    $class .= " cache-workaround";
  }

  foreach ($atts as $k => $v) {
    $data_attr .= ' data-' . preg_replace('/[^A-Za-z0-9]/', '-', $k) . '="' . esc_attr($v) . '"';
  }

  include(plugin_dir_path(__FILE__) . '../views/favorites_list_view.php');
}

function favorite_list_shortcode ($atts)
{
  ob_start();
  favorite_list($atts);
  $output = ob_get_clean();

  return $output;
}

add_shortcode('favorite_list', 'favorite_list_shortcode');