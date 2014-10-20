#!/usr/bin/env ../vendor/bin/phpunit
<?php

require_once '../shared/WebTest.php';
require_once 'Locators.inc';
require_once 'Settings.inc';

class devmateStorySolutionTest extends WebTest
{

    /**
     * @test
     * @group demo
     */

    public function StorySolutionTest()
    {
        $stories = require 'Stories.inc';
        $this->checkStory($stories,'storySolution');
    }

}

?>