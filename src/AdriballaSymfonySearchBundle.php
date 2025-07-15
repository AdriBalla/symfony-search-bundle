<?php

namespace Adriballa\SymfonySearchBundle;

use Elastic\Elasticsearch\Client;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class AdriballaSymfonySearchBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->scalarNode('index_prefix')->defaultValue('index-')->end()
            ->scalarNode('index_separator')->defaultValue('-')->end()
            ->scalarNode('elastic_dsn')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->parameters()
            ->set('adriballa-symfony-search.index_prefix', $config['index_prefix'])
            ->set('adriballa-symfony-search.index_separator', $config['index_separator'])
        ;

        $container->services()
            ->set(Client::class)
            ->factory(['Elastic\Elasticsearch\ClientBuilder', 'fromConfig'])
            ->args(['$config' => $this->getElasticsearchClientConfigFromDsn($config['elastic_dsn'])])
        ;

        $container->import('../config/services.yaml');
    }

    /**
     * @param  string  $elasticDsn
     * @return mixed[]
     */
    private function getElasticsearchClientConfigFromDsn(string $elasticDsn): array
    {
        $dsn = parse_url($elasticDsn);

        $elasticClientParameters = [];
        parse_str($dsn['query'] ?? '', $elasticClientParameters);

        $elasticClientParameters['SSLVerification'] = filter_var($elasticClientParameters['SSLVerification'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $elasticClientParameters['retries'] ??= 2;

        $host = sprintf('%s://%s:%s', $dsn['scheme'] ?? 'https', $dsn['host'] ?? 'elasticsearch', $dsn['port'] ?? 9200);

        return array_merge(
            [
                'hosts' => [$host],
                'basicAuthentication' => [
                    'username' => $dsn['user'] ?? 'admin',
                    'password' => $dsn['pass'] ?? 'admin',
                ],
            ],
            $elasticClientParameters,
        );
    }
}
