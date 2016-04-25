<?php

namespace Rhubarb\Leaf\Tests\Views;

use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Tests\RhubarbTestCase;
use Rhubarb\Leaf\Presenters\Presenter;
use Rhubarb\Leaf\Tests\Fixtures\Presenters\TestView;

class HtmlViewTest extends RhubarbTestCase
{
    public function testWrappers()
    {
        $presenter = new TestPresenter("Forename", true, true);
        $output = $presenter->generateResponse();

        // Careful now! The format of this string is important - don't be tidying it up!
        $this->assertEquals('<div id="Forename" class="TestView" presenter-name="Forename">
Dummy Output
<input type="hidden" name="ForenameState" id="ForenameState" value="{&quot;PresenterName&quot;:&quot;Forename&quot;,&quot;presenterPath&quot;:&quot;Forename&quot;}" />
</div>', $output);
    }

    public function testRaisingEventOnViewBridge()
    {
        $presenter = new TestPresenter("Forename", true, true);
        $presenter->test();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

        $view = $presenter->testView;
        $view->testRaiseEventOnViewBridge();
        $response = $presenter->generateResponse(new WebRequest());

        $content = $response->getContent();

        $this->assertContains('<event name="TestEvent" target="Forename"><param><![CDATA[123]]></param><param><![CDATA[234]]></param></event>', $content);

        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }
}

class TestPresenter extends Presenter
{
    private $requiresContainer = true;
    private $requiresStateInputs = true;
    public $testView;

    public function __construct($name = "", $requireContainer = true, $requireState = true)
    {
        parent::__construct($name);

        $this->requiresContainer = $requireContainer;
        $this->requiresStateInputs = $requireState;
    }

    protected function createView()
    {
        $this->testView = new TestView($this->requiresContainer, $this->requiresStateInputs);
        $this->registerView($this->testView);
    }
}
