<div class="wrap">
  <div id="icon-options-general"></div>
  <h2><?php _e('Favorites Plugin Panel')?></h2>
  <div class="alignleft actions bulkactions m10">
    <select name="action" id="action">
      <option value="false" selected="selected">Bulk Actions</option>
      <option value="add">Add Button</option>
      <option value="remove">Remove Button</option>
    </select>
    <input type="submit" name="" id="doaction" class="button action" value="Apply">
  </div>

  <table class="wp-list-table widefat fixed pages half-sized">
  <thead>
    <tr>
      <th scope="col" id="cb" class="manage-column column-cb check-column" style="">
        <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
        <input id="cb-select-all-1" type="checkbox">
      </th>
      <th scope="col" id="title" class="manage-column column-title sortable desc" style="">
        <span class="p10">Title</span>
      </th>
      <th scope="col" id="favorites" class="manage-column num sortable desc" style="">
        <span class="p10">Enable Favoriting</span>
      </th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th scope="col" class="manage-column column-cb check-column" style="">
        <label class="screen-reader-text" for="cb-select-all-2">Select All</label>
        <input id="cb-select-all-2" type="checkbox">
      </th>
      <th scope="col" id="title" class="manage-column column-title sortable desc" style="">
        <span class="p10">Title</span>
      </th>
      <th scope="col" id="favorites" class="manage-column num sortable desc" style="">
        <span class="p10">Enable Favoriting</span>
      </th>
    </tr>
  </tfoot>
  <thbody>
  <?php foreach($results as $result) { ?>
    <tr>
      <th scope="row" class="check-column">
        <label class="screen-reader-text" for="cb-select-<?php echo $result->getId(); ?>">Select A Post</label>
        <input id="cb-select-<?php echo $result->getId(); ?>" type="checkbox" name="post_id[]" post-id="<?php echo $result->getId(); ?>">
        <div class="locked-indicator"></div>
      </th>
      <td class="post-title page-title column-title">
        <a href="<?php echo $result->getPermalink(); ?>"><strong><?php echo $result->getTitle(); ?></strong></a>
      </td>
      <td>
        <?php
          $class   = 'unfavorited-star';
          if (preg_match('/\[favorite_button\]/', $result->getContent(), $match)) {
            $class = 'favorited-star';
          }
        ?>
        <a href="#" class="favorites">
          <span id="favorite-star-<?php echo $result->getId(); ?>" post-id="<?php echo $result->getId(); ?>" title="favorited" class="star-holder <?php echo $class; ?>"></span>
        </a>
      </td>
    </tr>
  <?php } ?>
  </thbody>
  </table>
</div>