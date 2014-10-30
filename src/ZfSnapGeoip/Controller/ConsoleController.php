<?php

/**
 * Console Controller
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */

namespace ZfSnapGeoip\Controller;

use Zend\Filter\File\Rename;
use Zend\Http\Client;
use Zend\Http\Response;
use ZfSnapGeoip\DatabaseConfig;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

class ConsoleController extends AbstractActionController
{
    /**
     * @var Console
     */
    protected $console;

    /**
     * Is quiet mode enabled?
     *
     * @var bool
     */
    protected $isQuiet;

    /**
     * @var DatabaseConfig
     */
    protected $config;

    /**
     * @param Console $console
     * @param DatabaseConfig $config
     */
    public function __construct(Console $console, DatabaseConfig $config)
    {
        $this->console = $console;
        $this->config  = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        if (!($request instanceof ConsoleRequest)) {
            throw new \RuntimeException('You can use this controller only from a console!');
        }
        return parent::dispatch($request, $response);
    }

    /**
     * Download GeoIP data via console
     */
    public function downloadAction()
    {
        $datFilePath = $this->config->getDatabasePath();
        $events      = $this->getEventManager();

        if ($this->getRequest()->getParam('no-clobber') && is_file($datFilePath)) {
            $events->trigger(__FUNCTION__ . '.exists', $this, array(
                'path' => $datFilePath,
            ));
            $this->writeline('Database already exist. Skipping...', Color::RED);
            return;
        }

        $source     = $this->config->getSource();

        $this->writeLine(sprintf('Downloading %s...', $source), Color::YELLOW);

        $adapter = new Client\Adapter\Socket();

        $client = new Client();
        $client->setUri($source);
        $client->setAdapter($adapter);
        $client->setMethod(Request::METHOD_GET);
        $response = $client->send();

        if ($response->getStatusCode() !== Response::STATUS_CODE_200) {
            $this->writeLine('Error during file download occured', Color::RED);
            return;
        } else {
            $events->trigger(__FUNCTION__ . '.pre', $this, array(
                'path' => $datFilePath,
            ));
            file_put_contents($datFilePath, gzdecode($response->getBody()));
        }

        $this->writeLine('Download completed', Color::GREEN);
        $this->writeLine('Unzip the downloading data...', Color::YELLOW);


        $events->trigger(__FUNCTION__ . '.post', $this, array(
            'path' => $datFilePath,
        ));

        $this->writeLine(sprintf('Unzip completed (%s)', $datFilePath), Color::GREEN);
    }

    /**
     * @param string $text
     * @param int $color
     * @param int $bgColor
     */
    private function writeLine($text, $color = null, $bgColor = null)
    {
        if ($this->isQuiet === null) {
            $this->isQuiet = $this->getRequest()->getParam('q', false);
        }
        if (!$this->isQuiet) {
            $this->console->writeLine($text, $color, $bgColor);
        }
    }
}

