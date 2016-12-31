// are we on an edit page for entry, category, global??
var $idInput = $('form[data-saveshortcut-redirect^="entries/"] input[type="hidden"][name="entryId"], form[data-saveshortcut-redirect^="categories/"] input[type="hidden"][name="categoryId"], form input[type="hidden"][name="setId"]');
var currentWarnings = {};

var warn = function(selector, collisions, message) {
  var msg = message.split('{user}');
  // get the old warnings
  var oldWarnings = currentWarnings[selector];

  if (!oldWarnings) { oldWarnings = {}; }
  // each person in new warnings:
  for (var i=0; i < collisions.length; i++) {
    var email = collisions[i].email;
  //   if in old warnings already, ignore
    if (!oldWarnings[email]) {
  //   otherwise add to old warnings, value = true
  //   and add to container
      oldWarnings[email] = true;
      $(selector).append('<div>'+msg[0]+'<a href="mailto:'+email+'">'+collisions[i].name+'</a>'+msg[1]+' <span>&times;</span></div>');
    }
  }
  // update current warnings
  currentWarnings[selector] = oldWarnings;
};

var globalPollInterval = null;

var lookForCollisions = function(elementId, warnSelector, pollInterval) {
  var actuallyLook = function () {
    var $warnContainer = $(warnSelector);
    var isModal = warnSelector !== '#mnsnitch';
    if (isModal && ! $warnContainer.closest('div.hud[style*="display: block;"]').length) {
      // our modal is 'gone'
      if (pollInterval) { window.clearInterval(pollInterval); }
    } else {
      Craft.postActionRequest(
        'mnSnitch/ajaxEnter',
        {elementId: elementId},
        function(response) {
          if (response['collisions'].length) {
            warn(warnSelector, response['collisions'], response['message']);
          }
          // look again every pollInterval seconds
          if (!pollInterval) {
            pollInterval = window.setInterval(actuallyLook, response['serverPollInterval'] * 1000);
          }
        }
      );
    }
  };
  actuallyLook();
};

$('body').on('dblclick', 'div.element.small', function() {
  window.setTimeout(function(event) {
    // get our container (there may be several, only one is displayed)
    var $modalContainer = $('div.hud[style*="display: block;"]');
    if ($modalContainer.length) {
      var $idInput = $modalContainer.find('input[type="hidden"][name="elementId"]');
      var namespace = $modalContainer.find('input[type="hidden"][name="namespace"]').val();
      var elementId = $modalContainer.find('input[type="hidden"][name="elementId"]').val();
      var pollInterval = null;
      $modalContainer.prepend('<div class="mnsnitch mnsnitch--modal" id="mnsnitch-'+namespace+'"></div>');
      lookForCollisions(elementId, '#mnsnitch-'+namespace, pollInterval);
    }
  }, 1000);
});

$('body').on('click', '.mnsnitch span', function() {
  var $div = $(this).closest('div');
  // remove it from the dom
  $div.remove();
});


if ($idInput.length) {
  // we have landed on an entry form page for an existing entry
  // add our message div
  $('body').prepend('<div class="mnsnitch mnsnitch--main" id="mnsnitch"></div');
  // check for collisions
  lookForCollisions($idInput.val(), '#mnsnitch', globalPollInterval);
}
