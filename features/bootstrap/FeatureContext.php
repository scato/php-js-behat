<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Driver\NodeJS\Server\ZombieServer;
use Behat\Mink\Driver\ZombieDriver;
use PhpJsBehat\FakeServer;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * @var ZombieDriver
     */
    private $driver;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $nodePath = __DIR__ . '/../../node_modules';
        putenv("NODE_PATH=$nodePath");

        $this->driver = new ZombieDriver(
            new ZombieServer()
        );

        $this->driver->start();
    }

    /**
     * @Given a fake API client
     */
    public function aFakeApiClient()
    {
        $this->driver->visit('about:blank');
        $this->driver->executeScript(file_get_contents(__DIR__ . '/../../src/js/FakeApiClient.js'));
        $this->driver->executeScript('window.client = new FakeApiClient();');
    }

    /**
     * @When I call the client
     */
    public function iCallTheClient()
    {
        $this->driver->executeScript('window.reply = undefined;');
        $this->driver->executeScript('window.client.call(\'foo\').then(function (reply) { window.reply = reply; });');
    }

    /**
     * @When I call the client with the wrong request
     */
    public function iCallTheClientWithTheWrongRequest()
    {
        $this->driver->executeScript('window.message = undefined;');
        $this->driver->executeScript('window.client.call(null).then(function () {}, function (message) { window.message = message; });');
    }

    /**
     * @When the server responds
     */
    public function theServerResponds()
    {
        $server = new FakeServer();

        $requests = $this->driver->evaluateScript('window.client.requests');

        foreach ($requests as $id => $request) {
            try {
                $response = $server->call($request);

                $this->driver->executeScript('window.client.resolvers[' . $id . '](' . json_encode($response) . ')');
            } catch (Exception $exception) {
                $this->driver->executeScript('window.client.rejecters[' . $id . '](' . json_encode($exception->getMessage()) . ')');
            }
        }
    }

    /**
     * @Then I should have a fake reply
     */
    public function iShouldHaveAFakeReply()
    {
        $assertion = $this->driver->evaluateScript('window.reply === \'bar\'');

        assert('$assertion');
    }

    /**
     * @Then I should have a fake error message
     */
    public function iShouldHaveAFakeErrorMessage()
    {
        $assertion = $this->driver->evaluateScript('window.message === \'Unexpected request\'');

        assert('$assertion');
    }
}
