jQuery(document).ready(function ($) {
  tinymce.PluginManager.add('favorite_list_button', function (editor, url) {
    editor.addButton('favorite_list_button_key', {
      title: 'Add Favorite List',
      image: url + '/../../images/list-icon.png',
      onclick: function(e) {
        var target = e.target.offsetParent;
        $(target).toggleClass('mce-active');

        $(target).hasClass('mce-active') ? editor.insertContent('[favorite_list posts_per_page=5 order="ASC"]')
                                         : editor.setContent(editor.getContent().replace(/\[favorite_list.*\]/g, ''));
      }
    });
  });
});