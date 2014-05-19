Handlebars.registerHelper('previous', function(page) {
  page = (page - 1 > 0) ? (page - 1) : 1;
  return new Handlebars.SafeString(
    '<a href="#" value="' + page + '" class="page">&lt; Previous</a>'
  );
});

Handlebars.registerHelper('next', function (has_more, page) {
  page = has_more ? page + 1 : page;
  return new Handlebars.SafeString(
    '<a href="#" value="' + page + '" class="page">Next &gt;</a>'
  );
});

Handlebars.registerHelper('current', function (page, per_page, total) {
  var total_pages = Math.ceil(total/per_page);
  return new Handlebars.SafeString(
    '<span class="current" value="' + page + '">' + page + '</span>/<span class="total" value="' + total_pages + '">' + total_pages + '</span>'
  );
});