<?php

namespace Drupal\app\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class AppTerminateSubscriber.
 */
class AppTerminateSubscriber implements EventSubscriberInterface {


  /**
   * Constructs a new AppTerminateSubscriber object.
   */
  public function __construct() {

  }

  /**
   * Code that should be triggered on event specified
   */
  public function onFinishRequest(FinishRequestEvent $event) {
  }

  /**
   * Code that should be triggered on event specified
   */
  public function onView(GetResponseForControllerResultEvent $event) {
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events[KernelEvents::FINISH_REQUEST][] = ['onFinishRequest'];
//    $events[KernelEvents::VIEW][] = ['onView'];
    return $events;
  }


}
