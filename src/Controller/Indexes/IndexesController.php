<?php

declare(strict_types=1);

namespace Adriballa\SymfonySearchBundle\Controller\Indexes;

use Adriballa\SymfonySearchBundle\Controller\Indexes\Request\CreateIndexRequest;
use Adriballa\SymfonySearchBundle\Controller\Indexes\Request\DeleteIndexRequest;
use Adriballa\SymfonySearchBundle\Services\Indexes\Client\IndexClientInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\Client\IndexMappingClientInterface;
use Adriballa\SymfonySearchBundle\Services\Indexes\DTO\Index;
use Adriballa\SymfonySearchBundle\Services\Indexes\Exceptions\IndexDefinitionNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class IndexesController extends AbstractController
{
    #[Route('/indexes', name: 'index.add', methods: ['POST'])]
    public function addIndex(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        IndexClientInterface $client,
    ): JsonResponse {
        try {
            /** @var CreateIndexRequest $createIndexRequest */
            $createIndexRequest = $serializer->deserialize($request->getContent(), CreateIndexRequest::class, 'json');

            $errors = $validator->validate($createIndexRequest);

            if (count($errors) > 0) {
                $errorMessages = [];

                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }

                return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
            }

            $index = $client->createIndex(
                $createIndexRequest->indexType,
                $createIndexRequest->addAlias,
                $createIndexRequest->deleteExisting,
            );

            return new JsonResponse($index);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/indexes', name: 'index.remove', methods: ['DELETE'])]
    public function removeIndex(
        Request $request,
        IndexClientInterface $client,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): JsonResponse {
        try {
            $deleteIndexRequest = $serializer->deserialize($request->getContent(), DeleteIndexRequest::class, 'json');

            $errors = $validator->validate($deleteIndexRequest);

            if (count($errors) > 0) {
                $errorMessages = [];

                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }

                return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
            }

            $client->deleteIndex($deleteIndexRequest->indexType);

            return new JsonResponse(null);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/indexes/{indexType}/filters', name: 'index.filters', methods: ['GET'])]
    public function getFilterablesFields(
        string $indexType,
        IndexMappingClientInterface $client,
    ): JsonResponse {
        try {
            return new JsonResponse($client->getFilterableFields(new Index($indexType)));
        } catch (IndexDefinitionNotFoundException) {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
    }

    #[Route('/indexes/{indexType}/sorts', name: 'index.sorts', methods: ['GET'])]
    public function getSortablesFields(
        string $indexType,
        IndexMappingClientInterface $client,
    ): JsonResponse {
        try {
            return new JsonResponse($client->getSortableFields(new Index($indexType)));
        } catch (IndexDefinitionNotFoundException) {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
    }
}
