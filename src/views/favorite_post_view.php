<li id="favorite-<?php echo $favorite->getId(); ?>" class="favorite">
  <?php favorite_toggle_button($favorite->getId()); ?>
  <a href="<?php echo $favorite->getPermalink(); ?>"
     rel="bookmark" class="favorite-link"
     title="<?php echo $favorite->getTitle(); ?>">
     <?php echo $favorite->getTitle(); ?>
  </a>
</li>