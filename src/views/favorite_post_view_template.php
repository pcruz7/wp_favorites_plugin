{{#each favorites}}
<li id="favorite-{{id}}" class="favorite">
  <div class="my-favorite">
    <a href="#"
       class="favorite-post-toggle favorite-post-on my-favorite-post-{{id}}"
       id="my-favorite-post-{{id}}"
       title="<?php _e('Remove from favorites', 'my-favorites'); ?>">

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
  <a href="{{permalink}}"
     rel="bookmark" class="favorite-link"
     title="{{title}}">
     {{title}}
  </a>
</li>
{{/each}}