<?php
  $tooltip = (in_array($post_id, $favorites))
              ? __('Remove from favorites', 'my-favorites')
              : __('Add to favorites', 'my-favorites');

  $class = (in_array($post_id, $favorites)) ? ' favorite-post-on' : ' favorite-post-off';

  if (!is_user_logged_in()) {
    $class  .= ' guest';
  }

  if (!$ajax && (!is_user_logged_in() || defined(WP_CACHE) && WP_CACHE)) {
    // Update button following AJAX call:
    $class  .= ' cache-workaround';
  }

  $class .= " size-24";
?>

<div class="my-favorite">
  <a href="#"
     class="favorite-post-toggle <?php echo $class; ?> my-favorite-post-<?php echo $post_id; ?>"
     id="my-favorite-post-<?php echo $post_id; ?>"
     title="<?php echo esc_attr($tooltip); ?>">

    <span class="favorite-post-on-button">
      <span class="label">
        <?php _e('Remove from favorites', 'my-favorites'); ?>
      </span>
    </span>

    <span class="favorite-post-off-button">
      <span class="label">
        <?php _e('Add to favorites', 'my-favorites'); ?>
      </span>
    </span>
  </a>
</div>