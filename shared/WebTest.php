<?php

abstract class WebTest extends PHPUnit_Framework_TestCase
{
    protected $locators = array();
    protected $verificationErrors = array();
    protected $webDriver;

    public function setUp()
    {
        $this->url = $GLOBALS['settings']['webserver'];
        $this->locators = $GLOBALS['webLocators'];
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => $GLOBALS['settings']['browser']);
        $this->webDriver = RemoteWebDriver::create($GLOBALS['settings']['selenium'], $capabilities);
        $this->setWindowSize(1200,600);
        $this->WebLog('Test: ' . $this->getName());
        $this->WebLog('Site: ' . $GLOBALS['settings']['webserver']);
    }

    public function setWindowSize($x, $y)
    {
        $this->webDriver->manage()->window()->setSize(new WebDriverDimension($x,$y));
    }

    public function assertPostConditions()
    {
        $this->assertEmpty($this->verificationErrors, "Test failed, see log for details. Errors count: " . count($this->verificationErrors));
    }

    public function tearDown()
    {
        $this->webDriver->close();
        $this->webDriver->quit();
    }

    public function open($path = '/')
    {
        $this->webDriver->get($this->url . $path);
    }

    public function WebLog($msg)
    {
        echo $this->getName() . ": " . $msg . "\n";
    }

    protected function stripcss($css)
    {
        if (substr($css, 0,4)=='css='){$css=substr($css, 4);};
        return $css;
    }

    protected function selector($css)
    {
// To know that a substring is at the start of the string, you must use:  
// === 0
        if(0===strpos($css,"//")){ // XPath
            return WebDriverBy::xpath($css);
        } else {          // css
            return WebDriverBy::cssSelector($this->stripcss($css));
        }
    }

    public function click($css)
    {
        try {
            $el = $this->webDriver->findElement($this->selector($css));
            $el->click();
        } catch (Exception $e) {
            array_push($this->verificationErrors, $e->GetMessage());
            echo $e->GetMessage() . "\n";
        }
    }

    public function clickAt($css,$loc)
    {
        try {
            list($x, $y) = explode(",", $loc, 2);            
            $el = $this->webDriver->findElement($this->selector($css));
            $this->webDriver->action()->moveToElement($el, (int)$x, (int)$y)->click()->perform();
        } catch (Exception $e) {
            array_push($this->verificationErrors, $e->GetMessage());
            echo $e->GetMessage() . "\n";
        }
    }

    public function mouseAt($css,$loc)
    {
        try {
            list($x, $y) = explode(",", $loc, 2);            
            $el = $this->webDriver->findElement($this->selector($css));
            $this->webDriver->action()->moveToElement($el, (int)$x, (int)$y)->perform();
        } catch (Exception $e) {
            array_push($this->verificationErrors, $e->GetMessage());
            echo $e->GetMessage() . "\n";
        }
    }

    public function mouseOver($css)
    {
       $this->mouseAt($css,"0,0");
    }
    
    public function type($css,$text)
    {
        try {
            $el = $this->webDriver->findElement($this->selector($css));
            $el->sendKeys($text);
        } catch (Exception $e) {
            array_push($this->verificationErrors, $e->GetMessage());
            echo $e->GetMessage() . "\n";
        }
    }

    public function pause($msec)
    {
        sleep($msec/1000);
    }

    public function getText($css)
    {
        try {
            $css = $this->stripcss($css);
            if($this->verifyElementPresent($css)){
                $el = $this->webDriver->findElement($this->selector($css));
                return $el->getText();
            }
        } catch (Exception $e) {
            array_push($this->verificationErrors, $e->GetMessage());
            echo $e->GetMessage() . "\n";
        }
    }

    public function verifyEquals($want, $got, $desc='')
    {
        try {
            $this->assertEquals($want, $got, $desc . $want . ' <> ' . $got);
        } catch (Exception $e) {
            array_push($this->verificationErrors, $desc.$e->GetMessage());
            echo $e->GetMessage() . "\n";
        }
    }

    public function verifyNotEquals($want, $got)
    {
        try {
            $this->assertNotEquals($want, $got, $want . ' == ' . $got);
        } catch (Exception $e) {
            array_push($this->verificationErrors, $e->GetMessage());
            echo $e->GetMessage() . "\n";
        }
    }

    public function getVarLocator($locatorName, $vars)
    {
        $locator = $this->locators[$locatorName];
        foreach ($vars as $key => $value) {
            $locator = str_replace('{'.$key.'}', $value, $locator);
        }

        return $locator;
    }

    public function verifyLocation($url)
    {
        $this->verifyEquals(parse_url($this->webDriver->getCurrentUrl(), PHP_URL_PATH), $url);
    }

    public function elementPresent($css)
    {
        $els = $this->webDriver->findElements($this->selector($css));
        return (0 < count($els));
    }

    public function getAttribute($loc)
    {
        try {
            list($css, $attr) = explode("@", $loc, 2);
            $element = $this->webDriver->findElement($this->selector($css));
            return $element->GetAttribute($attr);
        } catch (Exception $e) {
            array_push($this->verificationErrors, $e->GetMessage());
            echo $e->GetMessage() . "\n";
        }
    }

    public function getCssValue($cssSelector, $property)
    {
        $element = $this->webDriver->findElement($this->selector($cssSelector));
        return $element->getCSSValue($property);
    }

    public function transformColorToHex($color)
    {
        if ($color[0] != '#') {
            $color = str_replace('rgb(', '', $color);
            $color = str_replace('rgba(', '', $color);
            $color = str_replace(')', '', $color);
            $colorParts = explode(',', $color);
            $clr = intval($colorParts[0])*65536 + intval($colorParts[1])*256 + intval($colorParts[2]);
            $color = sprintf("#%06x",$clr);
        }
        if (strlen($color) == 4) {
            $color = '#' . $color[1] . $color[1] . $color[2] . $color[2] . $color[3] . $color[3];
        }
        return $color;
    }

    public function getColorHex($cssSelector)
    {
        $color = $this->getCssValue($cssSelector, 'color');
        return $this->transformColorToHex($color);
    }

    public function getBackgroundColorHex($cssSelector)
    {
        $color = $this->getCssValue($cssSelector, 'background-color');
        return $this->transformColorToHex($color);
    }

    /**
     * Create array of selectors to iterate over similar elements
     * @param $iterableSelector - selector with possible placeholders like {1-3}-{1-2}
     * @return array of selectors with placeholders replaced by each from start to end - (1-1,1-2,2-1,2-2,3-1,3-2)
     */
    public function processIterableSelector($iterableSelector)
    {
        $selectors = array();
        if (strpos($iterableSelector, '{') === false) {
            $selectors[] = $iterableSelector;
        } else {
            $startPos = strpos($iterableSelector, '{');
            $endPos = strpos($iterableSelector, '}');
            $iteratorDesc = substr($iterableSelector, $startPos + 1, $endPos - $startPos - 1);
            $iteratorDescFromTo = array_map(intval, explode('-', $iteratorDesc));
            for ($i = $iteratorDescFromTo[0]; $i <= $iteratorDescFromTo[1]; $i++) {
                $selectors = array_merge($selectors, $this->processIterableSelector(substr_replace($iterableSelector, $i, $startPos, $endPos - $startPos + 1)));
            }
        }
        return $selectors;
    }


    /**
     * @param $cssSelector
     * @return array of two coordinates: X followed by Y
     */
    public function getElementCoordinates($cssSelector)
    {
        try {
            $element = $this->webDriver->findElement($this->selector($cssSelector));
            $element_coordinates = $element->getLocation();
            return array($element_coordinates->getX(), $element_coordinates->getY());
        } catch (Exception $e) {
            array_push($this->verificationErrors, $e->GetMessage());
            echo $e->GetMessage() . "\n";
            return array(-1, -1);
        }
    }

    private function checkRules($pageRules)
    {
        foreach ($pageRules as $locatorName => $locatorRules) {
            $this->WebLog('Checking item: ' . $locatorName);
            $cssSelectors = $this->processIterableSelector($this->locators[$locatorName]);
            foreach ($cssSelectors as $cssSelector) {
                foreach ($locatorRules as $property => $expectedValue) {
                    if($this->elementPresent($cssSelector)){
                        switch ($property) {
                        case 'mouseAt':
                            $this->mouseAt($cssSelector,"5,5");
                            $value = $expectedValue;
                            break;
                        case 'visible':
                            $els = $this->webDriver->findElements($this->selector($this->locators[$locatorName]));
                            if (1 == count($els)){
                                $value = $els[0] -> isDisplayed();
                            }
                            else{
                                $value = false; // selector is does not return single element
                            }
                            break;
                        case 'gotoPath':
                            $this->open($expectedValue);
//                            sleep(12);
                        case 'checkPath':
                            $value = parse_url($this->webDriver->getCurrentUrl(), PHP_URL_PATH);
                            break;
                        case 'click':
                            $this->clickAt($this->locators[$locatorName],"10,10");
                            $value = $expectedValue;
                            break;
                        case 'pause':
                            sleep($expectedValue);
                            $value = $expectedValue;
                            break;
                        case 'border-bottom-color':
                        case 'border-top-color':
                        case 'border-right-color':
                        case 'border-left-color':
                        case 'border-color':
                        case 'background-color':
                        case 'color':
                            $color = $this->getCssValue($cssSelector, $property);
                            $value = $this->transformColorToHex($color);
                            break;
                        case 'hover-background':
                            $this->mouseAt($cssSelector,"5,5");
                            usleep(100000);
                            $value = $this->getBackgroundColorHex($cssSelector);
                            break;
//                        case 'hover':  // default - topleft
//                            $this->mouseAt($cssSelector,"5,5");
//                            usleep(100000);
//                            $value = $this->getColorHex($cssSelector);
//                            break;
                        case 'hover':
                            $el = $this->webDriver->findElement($this->selector($cssSelector));
                            $this->mouseAt($cssSelector,
                                strval($el->getSize()->getWidth()/2)
                                .",".
                                strval($el->getSize()->getHeight()/2)
                            ); // Center
                            usleep(100000);
                            $value = $this->getColorHex($cssSelector);
                            break;
                        case 'hover_topright':
                            $el = $this->webDriver->findElement($this->selector($cssSelector));
                            $this->mouseAt($cssSelector,
                                strval($el->getSize()->getWidth()-5).",5"); // top right corner
                            usleep(100000);
                            $value = $this->getColorHex($cssSelector);
                            break;
                        case 'hover_bottomleft':
                            $el = $this->webDriver->findElement($this->selector($cssSelector));
                            $this->mouseAt($cssSelector,
                                "5,".strval($el->getSize()->getHeight()-5)); // bottom left corner
                            usleep(100000);
                            $value = $this->getColorHex($cssSelector);
                            break;
                        case 'hover-opacity':
                            $this->mouseAt($cssSelector,"25,25");
                            usleep(800000);
                            $value = sprintf("%0.2f",floatval($this->getCssValue($cssSelector, 'opacity')));
                            break;
                        case 'uppercase':
                            $text = $this->getText($cssSelector);
                            $text = preg_replace('/[^A-Za-z0-9\-]/', '', $text); // remove all special chars
                            $value = ($text == strtoupper($text));
                            break;
                        case 'text':
                            $value = $this->getText($cssSelector);
                            break;
                        case 'font-family':
                            $value = str_replace(' ', '', str_replace('"', '',
                                         $this->getCssValue($cssSelector, $property)));
                            break;
                        case 'font-weight':
                            $value = $this->getCssValue($cssSelector, $property);
                            if (array_key_exists ( $value , $this->allowedfontweights )){
                                 $value = $this->allowedfontweights[$value];
                            }
                            break;
                        case 'src_path':
                            $value = $this->getAttribute($cssSelector.'@src');
                            $value = parse_url($value);
                            $value = $value['path'];
                            break;
                        case 'href_path':
                            $value = $this->getAttribute($cssSelector.'@href');
                            $value = parse_url($value);
                            $value = $value['path'];
                            break;
                        case 'background-image_path':
                            $value = $this->getCssValue($cssSelector,'background-image');
                            preg_match('/url\((.*)\)/',$value,$value);
                            $value = parse_url($value[1]);
                            $value = $value['path'];
                            break;
                        case 'href_start':
                            $url = $this->getAttribute($cssSelector.'@href');
                            $value = (substr_count($url,$expectedValue)>0)? $expectedValue: $url;
                            break;
                        case 'src':
                        case 'href':
                            $value = $this->getAttribute($cssSelector.'@'.$property);
                            break;
                        case 'src_start':
                            $url = $this->getAttribute($cssSelector.'@src');
                            $value = (substr_count($url,$expectedValue)>0)? $expectedValue: $url;
                            break;
                        case 'focused':
                            $el = $this->webDriver->findElement(WebDriverBy::cssSelector($cssSelector));
                            $value = $el->equals($this->webDriver->getActiveElement());
                            $value = ($value) ? 'true' : 'false';
                            break;
                        case 'alt':
                        case 'type':
                        case 'target':
                        case 'value':
                        case 'checked':
                        case 'placeholder':
                        case 'required':
                            $value = $this->getAttribute($cssSelector.'@'.$property);
                            break;
                        case 'screenshot':
                            $this->webDriver->takeScreenshot($expectedValue);
                            $value=$expectedValue;
                            break;
                        default:
                            $value = $this->getCssValue($cssSelector, $property);
                        }
                        $this->verifyEquals($expectedValue, $value, $property.': '.$cssSelector."\n");
                    }
                }
            }
        }    
    }

    public function checkStory($rules, $story)
    {
        $this->WebLog('Story check: '.$story);
        foreach($rules[$story] as $stepName => $step) {
            $this->WebLog('Executing step: '.$stepName);
            $this->checkRules($rules['steps'][$step]);
        }
    }
}

?>