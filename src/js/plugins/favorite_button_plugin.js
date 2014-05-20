jQuery(document).ready(function ($) {
  tinymce.PluginManager.add('favorite_button', function (editor, url) {
    editor.addButton('favorite_button_key', {
      title: 'Add Favorite Button',
      image: url + '/../../images/star-icon.png',
      onclick: function(e) {
        var target = e.target.offsetParent, content;
        $(target).toggleClass('mce-active');

        $(target).hasClass('mce-active') ? editor.insertContent('[favorite_button]')
                                         : editor.setContent(editor.getContent().replace(/\[favorite_button\]/g, ''));
      }
    });
  });
});