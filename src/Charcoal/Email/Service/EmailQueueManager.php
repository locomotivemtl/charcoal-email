<?php

namespace Charcoal\Email\Service;

// Dependencies from `charcoal-queue`
use Charcoal\Queue\AbstractQueueManager;

// Local namespace dependencies
use Charcoal\Email\Object\EmailQueueItem;

/**
 * Queue manager for emails.
 */
class EmailQueueManager extends AbstractQueueManager
{
    /**
     * Retrieve the queue item's model.
     *
     * @return EmailQueueItem
     */
    public function queueItemProto()
    {
        return $this->queueItemFactory()->create(EmailQueueItem::class);
    }
}
