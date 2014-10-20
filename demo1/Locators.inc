<?php

$GLOBALS['webLocators'] = array(
// Page
    'page'                   => '//html',

// Homepage
    'signupButton'           => 'div.head-slider a.btn-sign-up-now',
    'featuresLink'           => '//header//nav/a[text()="Features"]',

// Signup page
    'sellingOutsideBox'      => '//form[@id="form-signup"]//input[@name="selling_outside_app_store"]',
    'whatSolutionInput'      => '//form[@id="form-signup"]//input[@name="solution"]',

// Features/Frameworks
//    'frameworkUpdatesImage'  => '//div[@data-src="frameworks-0-0"]',
    'frameworkUpdatesImage'  => '//h2[text()="Sparkle-based Updates Framework"]/../../../preceding-sibling::div[1]//img',

// Features/App-management
    'easyUpdatesImage'  => '//h2[text()="Easy updates"]/../../../preceding-sibling::div[1]//img',

);

?>