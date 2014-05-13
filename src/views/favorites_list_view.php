<div class="my-favorite-post-list<?php echo $class ?>"<?php echo $data_attr; ?> >
<?php
  $repository = new FavoritesRepository(new CookieHandler());
  $interactor = new FavoritesInteractor($repository, FavoritesPlugin::LISTNAME);
  $favorites  = $interactor->favoriteList($atts);
?>
  <ul class="my-favorite-post-list">
  <?php foreach ($favorites as $favorite) { ?>
  <?php include('favorite_post_view.php'); ?>
  <?php } ?>
  </ul>
</div>