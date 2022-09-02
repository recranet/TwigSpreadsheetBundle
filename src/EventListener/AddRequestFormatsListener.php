<?php

namespace Recranet\TwigSpreadsheetBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class AddRequestFormatsListener.
 */
class AddRequestFormatsListener implements EventSubscriberInterface
{
    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $event->getRequest()->setFormat('csv', 'text/csv');
        $event->getRequest()->setFormat('ods', 'application/vnd.oasis.opendocument.spreadsheet');
        $event->getRequest()->setFormat('pdf', 'application/pdf');
        $event->getRequest()->setFormat('xls', 'application/vnd.ms-excel');
        $event->getRequest()->setFormat('xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 1],
        ];
    }
}
