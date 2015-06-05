<?php

/*
 * This file is part of the PrestaFaker package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PrestaFaker\Core;

use Monolog\Logger;
use Symfony\Component\EventDispatcher\GenericEvent;

class Listener
{
    const APP = 'app';
    const WEBSERVICE = 'webservice';
    const CATEGORY = 'category';
    const FEATURE = 'feature';
    const PRODUCT = 'product';

    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function onApp(GenericEvent $event, $name)
    {
        $this->on($event, self::APP, $name);
    }

    public function onWebservice(GenericEvent $event, $name)
    {
        $this->on($event, self::WEBSERVICE, $name);
    }

    public function onCategory(GenericEvent $event, $name)
    {
        $this->on($event, self::CATEGORY, $name);
    }


    public function onFeature(GenericEvent $event, $name)
    {
        $this->on($event, self::FEATURE, $name);
    }

    public function onProduct(GenericEvent $event, $name)
    {
        $this->on($event, self::PRODUCT, $name);
    }

    static public function buildEvent($message, $level = Logger::INFO, $subject = null)
    {
        return new GenericEvent($subject, array(
            'message' => $message,
            'level' => $level
        ));
    }


    private function on(GenericEvent $event, $type, $name)
    {
        try {
            $level = $event->getArgument('level');
        } catch (\InvalidArgumentException $e) {
            $level = Logger::INFO;
        }

        $this->logger->log($level, $this->format($event, $type, $name));
    }


    private function format(GenericEvent $event, $type, $name)
    {
        try {
            $message = $event->getArgument('message');
        } catch (\InvalidArgumentException $e) {
            $message = null;
        }

        return "[".$type."]\t".$name."\t".$message;
    }
}
