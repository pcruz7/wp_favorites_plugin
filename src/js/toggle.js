jQuery(document).ready(function ($) {
  var do_action = {
    'false' : function () {},
    'add'   : function (ids) {
      var request = {action: 'activate_post', ids: ids};
      $.post(ToggleAjax.ajaxurl, request, function (data, textStatus, xhr) {
        bulkAction(data.activated_id, function (id) {
          addFavoriteButton('#favorite-star-' + id);
        });
      });
    },
    'remove': function (ids) {
      var request = {action: 'deactivate_post', ids: ids};
      $.post(ToggleAjax.ajaxurl, request, function (data, textStatus, xhr) {
        bulkAction(data.deactivated_id, function (id) {
          removeFavoriteButton('#favorite-star-' + id);
        });
      });
    }
  }

  function bulkAction (posts, do_action) {
    posts.forEach(function (post) {
      if (post > -1) {
        do_action(post);
      }
    });
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

  function toggleFavoriteButton (target, postIds) {
    $.post(ToggleAjax.ajaxurl, {action: 'toggle_post', ids: postIds}, function (data, textStatus, xhr) {
      $(target).hasClass('favorited-star') ? removeFavoriteButton(target)
                                           : addFavoriteButton(target);
    });
  }

  function removeFavoriteButton (target) {
    $(target).removeClass('favorited-star');
    $(target).addClass('unfavorited-star');
  }

  function addFavoriteButton (target) {
    $(target).removeClass('unfavorited-star');
    $(target).addClass('favorited-star');
  }

  function getStarContainer (target) {
    return $(target).children()[0];
  }

  $('#doaction').click(function (ev) {
    var action = $('#action').find(':selected').attr('value'),
        values  = getSelected();
    if (values) {
      do_action[action](values);
    }
  });

  $('.favorites').click(function (ev) {
    var target = getStarContainer(ev.currentTarget);
    var postId = $(target).attr('post-id');
    toggleFavoriteButton(target, postId);
  });
});
