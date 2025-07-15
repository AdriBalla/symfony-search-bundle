<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Tests\Controllers\Search;

use Adriballa\SymfonySearchBundle\Controller\Search\Request\SearchIndexRequest;
use Adriballa\SymfonySearchBundle\Controller\Search\SearchController;
use Adriballa\SymfonySearchBundle\Services\Search\Client\SearchClientInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\SearchRequestFactoryInterface;
use Adriballa\SymfonySearchBundle\Services\Search\SearchRequest;
use Adriballa\SymfonySearchBundle\Services\Search\SearchResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[CoversClass(SearchController::class)]
class SearchControllerTest extends TestCase
{
    private MockObject&SearchClientInterface $searchClient;

    private MockObject&ValidatorInterface $validator;

    private MockObject&SerializerInterface $serializer;

    private MockObject&SearchRequestFactoryInterface $searchRequestFactory;

    private SearchController $controller;

    public function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->searchClient = $this->createMock(SearchClientInterface::class);
        $this->searchRequestFactory = $this->createMock(SearchRequestFactoryInterface::class);

        $this->controller = new SearchController();
    }

    public function testSearchSuccess(): void
    {
        $request = new Request([
            'query' => 'test',
            'searchFields' => ['field1', 'field2'],
            'start' => 0,
            'size' => 10,
            'filtersBy' => ['status' => 'active'],
            'aggregatesBy' => ['category'],
            'sortsBy' => ['name:asc'],
        ]);

        $searchIndexRequest = new SearchIndexRequest(
            'test',
            ['field1', 'field2'],
            0,
            10,
            ['status' => 'active'],
            ['category'],
            ['name:asc'],
        );

        $searchRequest = $this->createMock(SearchRequest::class);

        $searchResponse = new SearchResponse(
            indexType: 'test',
            success: true,
            start: 0,
            size: 10,
            duration: 1,
            totalHits: 1000,
            hits: [],
            aggregations: [],
        );

        $this->validator->method('validate')->willReturn(new ConstraintViolationList());

        $this->searchRequestFactory->expects($this->once())
            ->method('create')
            ->with('my_index', $searchIndexRequest)
            ->willReturn($searchRequest)
        ;

        $this->searchClient->expects($this->once())
            ->method('search')
            ->with($searchRequest)
            ->willReturn($searchResponse)
        ;

        $response = $this->controller->search('my_index', $request, $this->serializer, $this->validator, $this->searchRequestFactory, $this->searchClient);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($searchResponse), $response->getContent());
    }

    public function testSearchValidationError(): void
    {
        $request = new Request();

        $violation = new ConstraintViolation('Invalid query', null, [], '', 'query', '');

        $this->validator->method('validate')
            ->willReturn(new ConstraintViolationList([$violation]))
        ;

        $response = $this->controller->search('my_index', $request, $this->serializer, $this->validator, $this->searchRequestFactory, $this->searchClient);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['errors' => ['query' => 'Invalid query']]), $response->getContent());
    }

    public function testSearchException(): void
    {
        $request = new Request();

        $this->validator->method('validate')->willReturn(new ConstraintViolationList());

        $this->searchRequestFactory->method('create')->willThrowException(new \Exception('Search failed'));

        $response = $this->controller->search('my_index', $request, $this->serializer, $this->validator, $this->searchRequestFactory, $this->searchClient);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => 'Search failed']), $response->getContent());
    }
}
