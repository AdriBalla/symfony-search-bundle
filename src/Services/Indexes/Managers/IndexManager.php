<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Services\Indexes\Managers;

use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\IndexMapping;
use Adriballa\SymfonySearchBundle\Services\Indexes\Setting\IndexSettings;
use Adriballa\SymfonySearchBundle\Services\Indexes\Transformers\IndexSettingsTransformer;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class IndexManager implements IndexManagerInterface
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly IndexNameManagerInterface $indexNameManager,
        private readonly IndexSettingsTransformer $indexSettingsTransformer,
        private readonly Client $client,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function indexExists(Index $index): bool
    {
        if (null !== $index->getName()) {
            return $this->indexExistsRaw($index->getName());
        }

        return $this->client->indices()->exists([
            'index' => $this->indexNameManager->getAliasName($index->getType()),
        ])->asBool();
    }

    public function createIndex(IndexMapping $indexMapping, ?string $suffix = null): Index
    {
        $indexName = $this->indexNameManager->getIndexNameForType($indexMapping->name);

        $this->logger->info("Creation of new {$indexMapping->name} index under name {$indexName}");

        $this->client->indices()->create([
            'index' => $indexName,
            'body' => $indexMapping->configuration,
        ]);

        return new Index($indexMapping->name, $indexName);
    }

    public function copyIndex(string $indexFrom, string $indexTo, bool $wait = true): ?string
    {
        $this->logger->info('Copying index {$indexFrom} to {$indexTo}');
        $response = $this->client->reindex([
            'body' => [
                'source' => [
                    'index' => $indexFrom,
                ],
                'dest' => [
                    'index' => $indexTo,
                ],
            ],
            'wait_for_completion' => $wait,
        ]);

        $this->logger->info('Index {$indexFrom} has been copied to {$indexTo}');

        return $response['task'] ?? null;
    }

    public function refreshIndex(Index $index): void
    {
        $this->client->indices()->refresh([
            'index' => $index->getName(),
        ]);
        $this->logger->info("Index {$index->getName()} refreshed");
    }

    public function setIndexSetting(
        Index $index,
        IndexSettings $settings,
        ?string $indexName = null,
    ): void {
        $this->client->indices()->putSettings([
            'index' => $this->indexNameManager->getIndexName($index),
            'body' => $this->indexSettingsTransformer->transform($settings),
        ]);
    }

    public function deleteIndex(Index $index): bool
    {
        $indexName = $index->getName() ?? $this->getCurrentIndexNameForType($index->getType());

        if (null === $indexName) {
            return false;
        }

        $response = $this->client->indices()->delete(['index' => $indexName]);

        return $response['acknowledged'] ?? false;
    }

    public function addAliasOnIndex(Index $index, bool $deleteOld = false): void
    {
        $aliasName = $this->indexNameManager->getAliasName($index->getType());
        $aliasedIndexes = [];
        $actions = [[
            'add' => [
                'index' => $index->getName(),
                'alias' => $aliasName,
            ],
        ]];

        // Remove the alias on the previous index if necessary
        try {
            $alias = $this->client->indices()->getAlias(['name' => $aliasName]);
            $aliasedIndexes = array_keys($alias->asArray());

            foreach ($aliasedIndexes as $aliasedIndex) {
                array_unshift($actions, [
                    'remove' => [
                        'index' => $aliasedIndex,
                        'alias' => $aliasName,
                    ],
                ]);
                $this->logger->info("Removing alias {$aliasName} from index {$aliasedIndex}");
            }
        } catch (\Exception) {
            $this->logger->info("No alias {$aliasName} found. Nothing to delete");
        }

        // Performs the alias switch
        $this->client->indices()->updateAliases([
            'body' => [
                'actions' => $actions,
            ],
        ]);

        // Remove the old index if asked
        if (!$deleteOld) {
            return;
        }

        foreach ($aliasedIndexes as $indexToDelete) {
            $this->client->indices()->delete([
                'index' => $indexToDelete,
            ]);
        }
    }

    private function indexExistsRaw(string $indexName): bool
    {
        return $this->client->indices()->exists([
            'index' => $indexName,
        ])->asBool();
    }

    private function getCurrentIndexNameForType(string $indexType): ?string
    {
        $aliasName = $this->indexNameManager->getAliasName($indexType);

        try {
            $alias = $this->client->indices()->getAlias(['name' => $aliasName]);

            return implode(',', array_keys($alias->asArray()));
        } catch (ClientResponseException) {
            $this->logger->info("No alias {$aliasName} found.");
        }

        return null;
    }
}
