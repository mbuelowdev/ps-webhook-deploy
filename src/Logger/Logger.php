<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Logger;


use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class Logger
{
    /**
     * @var string
     */
    private static $name;

    /**
     * @var string
     */
    private static $logfile;

    /**
     * @var null|LoggerInterface
     */
    private static $instance = null;

    /**
     * @param ParameterBagInterface $parameterBag
     */
    public static function init(ParameterBagInterface $parameterBag)
    {
        self::$name = $parameterBag->get('logger.name');
        self::$logfile = $parameterBag->get('logger.logfile');
        self::$instance = self::getInstance();
    }

    /**
     * @return LoggerInterface|null
     */
    private static function getInstance()
    {
        if (!isset(self::$instance)) {
            try {
                // Create new LineFormatter with our line design
                $formatter = new LineFormatter(
                    "%datetime% %level_name% %message%\n",
                    'Y-m-d H:i:s'
                );

                // Create new StreamHandler instance
                $streamHandler = new StreamHandler(
                    self::$logfile,
                    \Monolog\Logger::DEBUG,
                    true,
                    null,
                    true
                );
                $streamHandler->setFormatter($formatter);

                $logger = new \Monolog\Logger(self::$name);
                $logger->pushProcessor(new PsrLogMessageProcessor());
                $logger->pushHandler($streamHandler);

                self::$instance = $logger;

            } catch (\Exception $e) {
                // Nothing we can do to be honest...
            }
        }

        return self::$instance;
    }

    /**
     * @param int $level
     * @param string $message
     */
    private static function log(int $level, string $message) : void
    {
        self::$instance->log($level, $message);
    }

    /**
     * @param string $message
     */
    public static function debug(string $message) : void
    {
        self::log(
            \Monolog\Logger::DEBUG,
            $message
        );
    }

    /**
     * @param string $message
     */
    public static function info(string $message) : void
    {
        self::log(
            \Monolog\Logger::INFO,
            $message
        );
    }

    /**
     * @param string $message
     */
    public static function warning(string $message) : void
    {
        self::log(
            \Monolog\Logger::WARNING,
            $message
        );
    }

    /**
     * @param string $message
     */
    public static function error(string $message) : void
    {
        self::log(
            \Monolog\Logger::ERROR,
            $message
        );
    }
}
