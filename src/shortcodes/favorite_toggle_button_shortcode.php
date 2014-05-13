<?php

function favorite_toggle_button($post_id, $atts = null)
{
  global $user_ID;

  $atts = extract(shortcode_atts(array(
    'ajax' => false,
  ), $atts));

  $repository = new FavoritesRepository(new CookieHandler());
  $favorites  = $repository->getFavoriteList(FavoritesPlugin::LISTNAME, $user_ID);

  include(plugin_dir_path(__FILE__) . '../views/favorites_toggle_button_view.php');
}

function favorite_toggle_button_shortcode ($atts)
{
  extract( shortcode_atts(array(
    'post_id' => get_the_ID(),
  ), $atts));

  ob_start();
  favorite_toggle_button($post_id);
  $output = ob_get_clean();

  return $output;
}

add_shortcode('favorite_button', 'favorite_toggle_button_shortcode');
