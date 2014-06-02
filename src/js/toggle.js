jQuery(document).ready(function ($) {
  var do_action = {
    'false': function () {},
    'add-button': function (ids) {
      var request = {action: 'activate_post', ids: ids};
      $.post(ToggleAjax.ajaxurl, request, function (data, textStatus, xhr) {
        bulkAction(data.activated_id, function (id) {
          addFavoriteButton('#favorite-star-' + id);
        });
      });
    },
    'remove-button': function (ids) {
      var request = {action: 'deactivate_post', ids: ids};
      $.post(ToggleAjax.ajaxurl, request, function (data, textStatus, xhr) {
        bulkAction(data.deactivated_id, function (id) {
          removeFavoriteButton('#favorite-star-' + id);
        });
      });
    },
    'add-list': function (ids) {
      var request = {action: 'activate_page_list', ids: ids};
      $.post(ToggleAjax.ajaxurl, request, function (data, textStatus, xhr) {
        bulkAction(data.activated_id, function (id) {
          addListing('#list-' + id);
        });
      });
    },
    'remove-list': function (ids) {
      var request = {action: 'deactivate_page_list', ids: ids};
      $.post(ToggleAjax.ajaxurl, request, function (data, textStatus, xhr) {
        bulkAction(data.deactivated_id, function (id) {
          removeListing('#list-' + id);
        });
      });
    }
  }

  function removeListing (target) {
    $(target).removeClass('list');
    $(target).addClass('no-list');
  }

  function addListing (target) {
    $(target).removeClass('no-list');
    $(target).addClass('list');
  }

  function removeFavoriteButton (target) {
    $(target).removeClass('favorited-star');
    $(target).addClass('unfavorited-star');
  }

  function addFavoriteButton (target) {
    $(target).removeClass('unfavorited-star');
    $(target).addClass('favorited-star');
  }

  function getContainer (target) {
    return $(target).children()[0];
  }

  function bulkAction (posts, do_action) {
    if (posts instanceof Array) {
      posts.forEach(function (post) {
        if (post > -1) {
          do_action(post);
        }
      });
    } else if (posts > -1) {
      do_action(posts);
    }
  }

  function getSelected () {
    var checked_values = $('input:checked'), ids = [];
    for (var i = 0; i < checked_values.length; i++) {
      post_id = $(checked_values[i]).attr('post-id');
      if (post_id) {
        ids.push(post_id);
      }
    }

    return ids;
  }

  function toggleFavoriteButton (target, postId) {
    $.post(ToggleAjax.ajaxurl, {action: 'toggle_post', ids: postId}, function (data, textStatus, xhr) {
      $(target).hasClass('favorited-star') ? removeFavoriteButton(target)
                                           : addFavoriteButton(target);
    });
  }

  function toggleListingButton (target, pageId) {
    $.post(ToggleAjax.ajaxurl, {action: 'toggle_page_list', ids: pageId}, function (data, textStatus, xhr) {
      $(target).hasClass('list') ? removeListing(target)
                                 : addListing(target);
    });
  }

  $('#doaction').click(function (ev) {
    var action = $('#action').find(':selected').attr('value'),
        values  = getSelected();
    if (values) {
      do_action[action](values);
    }
  });

  $('.favorites').click(function (ev) {
    var target = getContainer(ev.currentTarget);
    var postId = $(target).attr('post-id');
    toggleFavoriteButton(target, postId);
  });

  $('.lists').click(function (ev) {
    var target = getContainer(ev.currentTarget);
    var pageId = $(target).attr('page-id');
    toggleListingButton(target, pageId);
  });
});
