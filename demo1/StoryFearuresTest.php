#!/usr/bin/env ../vendor/bin/phpunit
<?php

require_once '../shared/WebTest.php';
require_once 'Locators.inc';
require_once 'Settings.inc';

class devmateStoryFeaturesTest extends WebTest
{

    /**
     * @test
     * @group demo
     */

    public function StoryFeaturesTest()
    {
        $stories = require 'Stories.inc';
        $this->checkStory($stories,'storyFeatures');
    }

}

?>