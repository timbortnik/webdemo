<?php

$webTest = array();
$webTest['steps'] = array();

$webTest['storySolution'] = array(
    'Open home page' => 'homepage',
    'Click signup and open signup page' => 'signup',
    'Check "Selling outside" box and see Solution input' => 'solution',
);

$webTest['storyFeatures'] = array(
    'Open home page' => 'homepage',
    'Click features and open features/frameworks page' => 'features',
    'Check Framework Updates image, screenshot' => 'frameworkUpdatesImage',
    'Open management page' => 'management',
    'Check Easy Updates image, screenshot' => 'easyUpdatesImage',
);


// Goes to homepage
$webTest['steps']['homepage'] = array(
    'page' => array(
        'gotoPath' => '/',
    ),
);

// Should be on homepage
// Clicks singup button and goes to signup page
$webTest['steps']['signup'] = array(
    'signupButton' => array(
        'click' => true,
    ),
    'page' => array(
        'checkPath' => '/signup',
    ),
);

// Should be on signup page
// Clicks on Selling Outside scheckbox and verifies solution input appearance
$webTest['steps']['solution'] = array(
    'sellingOutsideBox' => array(
        'click' => true,
    ),
    'whatSolutionInput' => array(
        'visible' => true,
    ),
);

// Should be on homepage
// Clicks features link and goes to features/frameworks page
$webTest['steps']['features'] = array(
    'featuresLink' => array(
        'click' => true,
    ),
    'page' => array(
        'checkPath' => '/features/frameworks',
    ),
);

// Should be on features/frameworks page
// Checks image and makes a screenshot
$webTest['steps']['frameworkUpdatesImage'] = array(
    'frameworkUpdatesImage' => array(
        'mouseAt' => true,
        'visible' => true,
        'width' => '495px',
        'height' => '380px',
        'src_path' => '/img/screens/features/frameworks/0-0-sparcle-updates-framework.jpg',
        'alt' => 'Sparkle-based updates framework',
        'screenshot' => 'html/screenshots/StoryFeaturesTest1.jpg',
    ),
);

// Goes to features/app-management
$webTest['steps']['management'] = array(
    'page' => array(
        'gotoPath' => '/features/app-management',
    ),
);

// Should be on features/app-management page
// Checks image and makes a screenshot
$webTest['steps']['easyUpdatesImage'] = array(
    'easyUpdatesImage' => array(
        'mouseAt' => true,
        'visible' => true,
        'width' => '440px',
        'height' => '324px',
        'src_path' => '/img/screens/features/app-management/1-0-easy-updates.jpg',
        'alt' => 'Easy Updates',
        'screenshot' => 'html/screenshots/StoryFeaturesTest2.jpg',
    ),
);

return $webTest;

?>