<?php

class FavoritesWidget extends WP_Widget
{
  function __construct()
  {
    parent::__construct(
      'favorite_posts',
      'My Favorite Posts',
      array('description' => __( 'Displays a list of posts favorited by the user.' ))
    );
  }

  public function form($instance)
  {
    $title    = isset( $instance['title'] ) ? esc_attr( $instance['title'] )  : '';
    $number   = isset( $instance['number'] ) ? absint( $instance['number'] )   : 5;
    include(plugin_dir_path(__FILE__) . '../views/widget_form_view.php');
  }

  public function update($new_instance, $old_instance)
  {
    $instance = (array) $old_instance;
    $instance['title']  = strip_tags( $new_instance['title'] );
    $instance['number'] = absint( $new_instance['number'] );
    return $instance;
  }

  public function widget($args, $instance)
  {
    extract( $args );

    $number = absint( $instance['number'] );
    $title  = apply_filters( 'widget_title', empty( $instance['title'] )
                ? __( 'Favorite Posts', 'my-favorites' )
                : $instance['title'], $instance, $this->id_base );

    if (!$number)
      $number = 5;

    ob_start();
    favorite_list(array('posts_per_page' => $number));
    $output = ob_get_clean();

    if (!empty($output)) {
      echo $before_widget;
      if ($title)
        echo $before_title . $title . $after_title;
      echo $output;
      echo $after_widget;
    }
  }
}

add_action('widgets_init', create_function('', 'register_widget("FavoritesWidget");'));
