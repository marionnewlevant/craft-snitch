<?php
/**
 * Snitch plugin for Craft CMS 3.x
 *
 * Report when two people might be editing the same entry, category, or global
 *
 * @copyright Copyright (c) 2019 Marion Newlevant
 */

/**
 * Snitch config.php
 *
 * This file exists only as a template for the Snitch settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'snitch.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

return [
    'serverPollInterval' => 2, // interval for polling server (in seconds)
    'messageTemplate' => 'May also be edited by: <a href="mailto:{{user.email}}">{{user.username}}</a>.', // warning message
    // These are the selectors for the hidden input
    // fields with the id of whatever we are editing.
    // These selectors need to be this specific - [name$="Id"] would pick up
    // both the sourceId and the sectionId on an entry form, and we want only sourceId.
    // Snitch looks for simultaneous editing of
    // elements and fields
    'elementInputIdSelector' =>
       'form input[type="hidden"][name="sourceId"]' // entry forms
        .', form input[type="hidden"][name="elementId"]' // modals entry forms
        .', form input[type="hidden"][name="setId"]' // global set
        .', form input[type="hidden"][name="categoryId"]' // category
        .', form input[type="hidden"][name="userId"]' // user
        .', form input[type="hidden"][name="productId"]', // product (does not handle product/variant conflicts)
    'fieldInputIdSelector' => 'form input[type="hidden"][name="fieldId"]',
];
