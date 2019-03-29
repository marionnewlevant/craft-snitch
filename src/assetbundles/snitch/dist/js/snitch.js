/*
This javascript file is injected on every back-end page. Here is what it does:
1) Fetch the snitch config values, store them as globals
2) Look for edit forms on this page - poll, because we want to find any modal edit windows that may appear.
For each edit form, we add the <div class="snitch"></div> warning container where any warnings will be displayed,
and start polling the server to look for conflicts.
When conflicts are reported, they are passed to warn(), which collects the current warnings from the warning
container (indexed by email address), and adds any new ones. Warnings are never removed, because a change
might have been made even if the conflicting editor is no longer editing.
When the close button on a warning is clicked, the warning class is changed, and css will hide the warning.
 */

// unchanging values from the config file
var globalPollInterval = null;
var globalMessage = null;
var globalInputIdSelector = null;
var snitchCollisionAjaxEnterLastRequest = null;

var currentWarnings = function($warnContainer) {
  var $warnings = $warnContainer.children('div');
  var ret = {};

  $warnings.each(function() {
    var email = $(this).data('email');
    ret[email] = true;
  });
  return ret;
};

var warn = function(elementId, $warnContainer, collisions) {
  var msg = globalMessage.split('{user}');
  // get the old warnings
  var oldWarnings = currentWarnings($warnContainer);

  // each person in new warnings:
  for (var i=0; i < collisions.length; i++) {
    var email = collisions[i].email;
    // if in old warnings already, ignore
    if (!oldWarnings[email]) {
      // otherwise add to container
      $warnContainer.append('<div data-email="'+email+'"><div>'+msg[0]+'<a href="mailto:'+email+'">'+collisions[i].name+'</a>'+msg[1]+' <span>&times;</span></div></div>');
    }
  }
};

// our close button click handler
$('body').on('click', '.snitch span', function() {
  var $div = $(this).closest('div[data-email]');
  // hide it with css
  $div.addClass('snitch--hidden');
});

var lookForEditForms = function() {
  // find all the hidden id input fields on the page (in main form and any modal forms)
  var $idInputs = $(globalInputIdSelector);

  $idInputs.each(function() {
    var $thisIdInput = $(this);
    var elementId = $thisIdInput.val();
    var $form = $thisIdInput.closest('form');
    var isModal = $form.hasClass('body');
    var snitchData = $form.data('snitch');
    var intervalId = null;
    var lookForConflicts = function() {
      var $warnContainer = isModal ? $form.children('.snitch--modal') : $('.snitch--main');
      if (isModal && !$form.closest('.hud.has-footer[style*="display: block;"]').length) {
        // our modal is gone.
        if (intervalId) { window.clearInterval(intervalId); }
      } else {
        if (!snitchCollisionAjaxEnterLastRequest
          || (snitchCollisionAjaxEnterLastRequest && snitchCollisionAjaxEnterLastRequest.status)) {
          snitchCollisionAjaxEnterLastRequest = Craft.postActionRequest(
            'snitch/collision/ajax-enter',
            {elementId: elementId},
            function(response, textStatus) {
              if (textStatus == 'success' && response && response['collisions'].length) {
                warn(elementId, $warnContainer, response['collisions']);
              }
            }
          );
        }
      }
    };

    if (!snitchData) {
      if (isModal) {
        $form.prepend('<div class="snitch snitch--modal"></div>');
      } else {
        $('body').prepend('<div class="snitch snitch--main"></div>');
      }
      $form.data('snitch', elementId);
      lookForConflicts();
      intervalId = window.setInterval(lookForConflicts, globalPollInterval);
    }
  });
};

// get our configuration, and once we have that, start polling for edit forms
var doEverything = function() {
  Craft.postActionRequest(
      'snitch/collision/get-config',
      {},
      function(response, textStatus) {
        if (textStatus == 'success' && response) {
          globalPollInterval = response['serverPollInterval'] * 1000;
          globalMessage = response['message'];
          globalInputIdSelector = response['inputIdSelector'];
          lookForEditForms();
          window.setInterval(lookForEditForms, globalPollInterval);
        }
      }
  );
};

doEverything();
