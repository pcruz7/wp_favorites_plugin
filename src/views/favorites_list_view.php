<script id="favorite_post_template" type="text/x-handlebars-template">
  <?php include('favorite_post_view_template.php'); ?>
</script>
<script type="text/x-handlebars-template" id="paging_template">
  <?php include('paging_view_template.php'); ?>
</script>
<div id="my-favorite-list-data" class="my-favorite-post-list<?php echo $class ?>"<?php echo $data_attr; ?> >
  <ul class="my-favorite-post-list">
  </ul>
  <div id="my-favorite-list-paging">
  </div>
</div>