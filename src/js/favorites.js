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

  function getTemplate (template, data) {
    var source   = $(template).html(),
        template = Handlebars.compile(source);
    return template(data);
  };

  function totalPerPage () {
    return parseInt($('#my-favorite-list-data').attr('data-posts-per-page'));
  };

  function currentPage () {
    return $('.current').attr('value');
  };

  function numFavoritesDisplaying () {
    return $('li.favorite').length;
  }

  function showResults (data) {
    $('ul.my-favorite-post-list').html(getTemplate("#favorite_post_template", {favorites: data}));
  };

  function showPaging (data) {
    $('#my-favorite-list-paging').html(getTemplate('#paging_template', data));
  };

  function removeFromFavoriteList (id) {
    $('#favorite-' + id).remove();
    toggleOff(id);

    var page = numFavoritesDisplaying() == 0 ? currentPage() - 1 : currentPage();
    if (page == 0) page++;

    fetchPage(page).then(addBindings);
  };

  function addToFavoriteList (id) {
    var request = {action: 'get_by_id', post_id: id};
    if (numFavoritesDisplaying() < totalPerPage()) {
      $.post(FavoritesAjax.ajaxurl, request, function (data, textStatus, xhr) {
        var template = getTemplate('#favorite_post_template', {favorites: [data]});
        $('ul.my-favorite-post-list').append(template);
        $('.favorite-post-toggle').unbind();
        bindToggleButtons();
        toggleOn(id);
      });
    } else {
      fetchPage(currentPage()).then(function () {
        addBindings();
        toggleOn(id);
      });
    }
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

  function bindPaging () {
    $('.my-favorite-page').click(function () {
      var page    = parseInt($(this).attr('value'));
          current = currentPage();
      if (page != current) {
        fetchPage(page).then(addBindings);
      }
    });
  };

  function addBindings () {
    $('.favorite-post-toggle').unbind();
    $('.page').unbind();
    bindToggleButtons();
    bindPaging();
  };

  function fetchPage (page) {
    var request = {
      action         : 'favorite_list',
      paged          : page,
      posts_per_page : totalPerPage(),
      order          : 'ASC',
      orderby        : 'post__in'
    };
    return $.post(FavoritesAjax.ajaxurl, request, function (data, textStatus, xhr) {
      showResults(data.results);
      showPaging(data);
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

  fetchPage(1).then(addBindings);
});
