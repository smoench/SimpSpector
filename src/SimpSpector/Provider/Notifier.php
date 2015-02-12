<?php

namespace SimpSpector\Provider;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SimpleThings\AppBundle\Entity\MergeRequest;

/**
 * @author David Badura <d.a.badura@gmail.com>
 * @author Simon Mönch <simonmoench@gmail.com>
 */
class Notifier
{
    /**
     * @var ProviderAdapterInterface;
     */
    private $adapter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ProviderAdapterInterface $adapter
     * @param LoggerInterface          $logger
     */
    public function __construct(ProviderAdapterInterface $adapter, LoggerInterface $logger = null)
    {
        $this->adapter = $adapter;
        $this->logger  = $logger ?: new NullLogger();
    }

    /**
     * @param MergeRequest $mergeRequest
     */
    public function notify(MergeRequest $mergeRequest)
    {
        $response = $this->adapter->addMergeRequestComment($mergeRequest);

        $this->logger->info('notify ' . $this->adapter->getName(), $response);
    }
}
