jQuery(document).ready(function ($) {
  function toggleOff (id) {
    id = 'my-favorite-post-' + id;
    $('.' + id).removeClass('favorite-post-on');
    $('.' + id).addClass('favorite-post-off');
    $('.' + id).attr('title', $('#' + id + ' .favorite-post-on-button .label').html().trim());
  };

  function toggleOn (id) {
    id = 'my-favorite-post-' + id;
    $('.' + id).removeClass('favorite-post-off');
    $('.' + id).addClass('favorite-post-on');
    $('.' + id).attr('title', $('#' + id + ' .favorite-post-off-button .label').html().trim());
  };

  function removeFromFavoriteList (id) {
    $('#favorite-' + id).remove();
    toggleOff(id);
  };

  function addToFavoriteList (id) {
    var request = {action: 'get_by_id', post_id: id};
    $.post(FavoritesAjax.ajaxurl, request, function (data, textStatus, xhr) {
      $('ul.my-favorite-post-list').append(data);
      $('.favorite-post-toggle').unbind();
      bindToggleButtons();
      toggleOn(id);
    });
  };

  function bindToggleButtons () {
    $('.favorite-post-toggle').click(function () {
      var id      = $(this).attr('id'),
          post_id = id.replace(/[^\d]+/g, ""),
          request = {action: 'toggle_favorite', post_id: post_id};

      $.post(FavoritesAjax.ajaxurl, request, function (response, textStatus, xhr) {
        var toggled = response.post_id == -1;
        toggled ? removeFromFavoriteList(post_id) : addToFavoriteList(post_id);
      }.bind(this));
    });
  };

  $('.favorite-post-toggle.cache-workaround').each(function (i, el) {
    var id      = $(this).attr('id'),
        post_id = id.replace(/[^\d]+/g, "");
        request = {action: 'is_favorite', post_id: post_id};

    $.post(FavoritesAjax.ajaxurl, request, function (response, textStatus, xhr) {
      response ? toggleOn(post_id) : toggleOff(post_id);
    }.bind(this));
  });

  bindToggleButtons();
});