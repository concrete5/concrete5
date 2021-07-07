<?php

namespace Concrete\Job;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Express\Search\Index\EntityIndex;
use Concrete\Core\File\File;
use Concrete\Core\Job\QueueableJob;
use Concrete\Core\Page\Page;
use Concrete\Core\Search\Index\IndexManagerInterface;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Punic\Misc as PunicMisc;
use ZendQueue\Message as ZendQueueMessage;
use ZendQueue\Queue as ZendQueue;

class IndexSearchAll extends QueueableJob
{
    // A flag for clearing the index
    const CLEAR = '-1';
    const CLEAR_EXPRESS_ENTITY = '-2';

    public $jQueueBatchSize = 50;
    public $jNotUninstallable = 1;
    public $jSupportsQueue = true;

    /** @var array The result from the last queue item */
    protected $result;

    protected $clearTable = true;

    /*
     * @var \Concrete\Core\Search\Index\IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function getJobName()
    {
        return t('Index Search Engine - All');
    }

    public function getJobDescription()
    {
        return t('Empties the page search index and reindexes all pages.');
    }

    public function __construct(IndexManagerInterface $indexManager, Connection $connection, ObjectManager $objectManager)
    {
        $this->indexManager = $indexManager;
        $this->connection = $connection;
        $this->objectManager = $objectManager;
    }

    public function start(ZendQueue $queue)
    {
        if ($this->clearTable) {
            // Send a "clear" queue item to clear out the index
            $queue->send(self::CLEAR);
        }

        // Queue everything
        foreach ($this->queueMessages() as $message) {
            $queue->send($message);
        }
    }

    /**
     * Messages to add to the queue.
     *
     * @return \Iterator
     */
    protected function queueMessages()
    {
        $pages = $users = $files = $sites = $objects = $entries = 0;

        foreach ($this->expressObjectsToQueue() as $id) {
            yield self::CLEAR_EXPRESS_ENTITY . $id;
            $objects++;
        }

        foreach ($this->pagesToQueue() as $id) {
            yield "P{$id}";
            $pages++;
        }
        foreach ($this->usersToQueue() as $id) {
            yield "U{$id}";
            $users++;
        }
        foreach ($this->filesToQueue() as $id) {
            yield "F{$id}";
            $files++;
        }
        foreach ($this->sitesToQueue() as $id) {
            yield "S{$id}";
            $sites++;
        }

        foreach ($this->expressEntriesToQueue() as $id) {
            yield "E{$id}";
            $entries++;
        }

        // Yield the result very last
        yield 'R' . json_encode([$pages, $users, $files, $sites, $objects, $entries]);
    }

    public function processQueueItem(ZendQueueMessage $msg)
    {
        $index = $this->indexManager;

        // Handle a "clear" message
        if (substr($msg->body, 0, 2) === '-2') {
            $this->clearExpressEntityIndex(substr($msg->body, 2));
        } else if ($msg->body == self::CLEAR) {
            $this->clearIndex($index);
        } else {
            $body = $msg->body;

            $message = substr($body, 1);
            $type = $body[0];

            $map = [
                'P' => Page::class,
                'U' => User::class,
                'F' => File::class,
                'S' => Site::class,
                'E' => Entry::class,
            ];

            if (isset($map[$type])) {
                $index->index($map[$type], $message);
            } elseif ($type === 'R') {
                // Store this result, this is likely the last item.
                $this->result = json_decode($message);
            }
        }
    }

    public function finish(ZendQueue $q)
    {
        if ($this->result) {
            list($pages, $users, $files, $sites, $objects, $entries) = $this->result;
            return t(
                'Index performed on: %s',
                PunicMisc::join([
                    t2('%d page', '%d pages', $pages),
                    t2('%d user', '%d users', $users),
                    t2('%d file', '%d files', $files),
                    t2('%d site', '%d sites', $sites),
                    t2('%d Express object', '%d Express objects', $objects),
                    t2('%d Express entry', '%d Express entries', $entries),
                ])
            );
        } else {
            return t('Indexed pages, users, files, sites and express data.');
        }
    }

    /**
     * @inheritDoc
     *
     * @see \Concrete\Core\Job\QueueableJob::executeBatch()
     */
    public function executeBatch($batch, ZendQueue $queue)
    {
        parent::executeBatch($batch, $queue);

        $em = Facade::getFacadeApplication()->make(EntityManagerInterface::class);
        $em->clear();
    }

    protected function clearExpressEntityIndex($id)
    {
        $object = $this->objectManager->getObjectByID($id);
        if ($object) {
            $app = Facade::getFacadeApplication();
            $index = $app->make(EntityIndex::class, ['entity' => $object]);
            $index->clear();
        }
    }

    /**
     * Clear out all indexes.
     *
     * @param $index
     */
    protected function clearIndex($index)
    {
        $index->clear(Page::class);
        $index->clear(User::class);
        $index->clear(File::class);
        $index->clear(Site::class);
    }

    /**
     * Get Pages to add to the queue.
     *
     * @return \Iterator
     */
    protected function pagesToQueue()
    {
        $qb = $this->connection->createQueryBuilder();

        // Find all pages that need indexing
        $query = $qb
            ->select('p.cID')
            ->from('Pages', 'p')
            ->leftJoin('p', 'CollectionSearchIndexAttributes', 'a', 'p.cID = a.cID')
            ->where('cIsActive = 1')
            ->andWhere($qb->expr()->orX(
                'a.ak_exclude_search_index is null',
                'a.ak_exclude_search_index = 0'
            ))->execute();

        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }

    /**
     * Get Users to add to the queue.
     *
     * @return \Iterator
     */
    protected function usersToQueue()
    {
        /** @var Connection $db */
        $db = $this->connection;

        $query = $db->executeQuery('SELECT uID FROM Users WHERE uIsActive = 1');
        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }

    /**
     * Get Express objects to add to the queue.
     *
     * @return \Iterator
     */
    protected function expressObjectsToQueue()
    {
        /** @var Connection $db */
        $db = $this->connection;

        $query = $db->executeQuery('SELECT id FROM ExpressEntities order by id asc');
        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }

    /**
     * Get Express entries to add to the queue.
     *
     * @return \Iterator
     */
    protected function expressEntriesToQueue()
    {
        /** @var Connection $db */
        $db = $this->connection;

        $query = $db->executeQuery('SELECT exEntryID FROM ExpressEntityEntries order by exEntryID asc');
        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }


    /**
     * Get Files to add to the queue.
     *
     * @return \Iterator
     */
    protected function filesToQueue()
    {
        /** @var Connection $db */
        $db = $this->connection;

        $query = $db->executeQuery('SELECT fID FROM Files');
        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }

    /**
     * Get Sites to add to the queue.
     *
     * @return \Iterator
     */
    protected function sitesToQueue()
    {
        /** @var Connection $db */
        $db = $this->connection;

        $query = $db->executeQuery('SELECT siteID FROM Sites');
        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }
}
