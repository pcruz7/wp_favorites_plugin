Handlebars.registerHelper('compare', function (lvalue, rvalue, options) {
  if (arguments.length < 3) {
    throw new Error("Handlerbars Helper 'compare' needs 2 parameters");
  }

  operator = options.hash.operator || "==";

  var operators = {
    '==':     function(l,r) { return l == r; },
    '===':    function(l,r) { return l === r; },
    '!=':     function(l,r) { return l != r; },
    '<':      function(l,r) { return l < r; },
    '>':      function(l,r) { return l > r; },
    '<=':     function(l,r) { return l <= r; },
    '>=':     function(l,r) { return l >= r; },
    'typeof': function(l,r) { return typeof l == r; }
  }

  if (!operators[operator]) {
    throw new Error("Handlerbars Helper 'compare' doesn't know the operator " + operator);
  }

  var result = operators[operator](lvalue,rvalue);
  return (result ? options.fn(this) : options.inverse(this));
});

Handlebars.registerHelper('previous', function(page) {
  page = (page - 1 > 0) ? (page - 1) : 1;
  return new Handlebars.SafeString(
    '<a href="#" value="' + page + '" class="my-favorite-page">&lt; Previous</a>'
  );
});

Handlebars.registerHelper('next', function (has_more, page) {
  page = has_more ? page + 1 : page;
  return new Handlebars.SafeString(
    '<a href="#" value="' + page + '" class="my-favorite-page">Next &gt;</a>'
  );
});

Handlebars.registerHelper('current', function (page, per_page, total) {
  var total_pages = Math.ceil(total/per_page);
  return new Handlebars.SafeString(
    '<span class="current" value="' + page + '">' + page + '</span>/<span class="total" value="' + total_pages + '">' + total_pages + '</span>'
  );
});