jQuery(document).ready(function ($) {

  function isEnabledWithContent (regex, content, target) {
    var has_shortcode = regex.test(content);
    return $(target).hasClass('mce-active') == has_shortcode;
  };

  function toggleButton (shortcode, regex, ev, editor) {
    var target  = ev.target.offsetParent,
        content = editor.getContent();

    if (!isEnabledWithContent(regex, content, target)) {
      $(target).toggleClass('mce-active');
      return;
    }

    $(target).toggleClass('mce-active');
    $(target).hasClass('mce-active') ? editor.insertContent(shortcode)
                                     : editor.setContent(editor.getContent().replace(regex, ''));

  };

  tinymce.create('tinymce.plugins.favorites_plugin', {
    init: function (editor, url) {
      editor.addButton('favorite_list_button_key', {
        title: 'Add Favorite List',
        image: url + '/../../images/list-icon.png',
        onclick: function(e) {
          toggleButton('[favorite_list posts_per_page=5 order="ASC"]', new RegExp(/\[favorite_list.*\]/g), e, editor);
        }
      });

      editor.addButton('favorite_button_key', {
        title: 'Add Favorite Button',
        image: url + '/../../images/star-icon.png',
        onclick: function(e) {
          toggleButton('[favorite_button]', new RegExp(/\[favorite_button\]/g), e, editor);
        }
      });
    }
  });

  tinymce.PluginManager.add('favorites_plugin', tinymce.plugins.favorites_plugin);
});