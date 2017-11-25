<?php

/**
 * Snitch Configuration
 */

return [
    'serverPollInterval' => 2, // interval for polling server (in seconds)
    'message' => 'May also be edited by: {user}.', // warning message
    // This selector needs to be this specific - [name$="Id"] would pick up
    // both the entryId and the sectionId on an entry form, and we only want
    // entryId. That is also why we can't check for collisions in the admin area,
    // as the id field for editing a section is 'sectionId'
    'inputIdSelector' => 'form input[type="hidden"][name="entryId"]' // entry forms
        .', form input[type="hidden"][name="elementId"]' // modals entry forms
        .', form input[type="hidden"][name="setId"]' // global set
        .', form input[type="hidden"][name="categoryId"]' // category
        .', form input[type="hidden"][name="userId"]', // user
];
