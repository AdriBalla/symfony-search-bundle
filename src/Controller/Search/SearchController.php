<?php

namespace Adriballa\SymfonySearchBundle\Controller\Search;

use Adriballa\SymfonySearchBundle\Controller\Search\Request\SearchIndexRequest;
use Adriballa\SymfonySearchBundle\Services\Search\Client\SearchClientInterface;
use Adriballa\SymfonySearchBundle\Services\Search\Factories\SearchRequestFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class SearchController extends AbstractController
{
    #[Route('/indexes/{indexType}/search', name: 'search', methods: ['GET'])]
    public function search(
        string $indexType,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        SearchRequestFactoryInterface $searchRequestFactory,
        SearchClientInterface $client,
    ): JsonResponse {
        try {
            $searchIndexRequest = new SearchIndexRequest(
                $request->query->get('query', null),
                $request->query->all('searchFields'),
                $request->query->getInt('start'),
                $request->query->getInt('size'),
                $request->query->all('filtersBy'),
                $request->query->all('aggregatesBy'),
                $request->query->all('sortsBy'),
            );

            $errors = $validator->validate($searchIndexRequest);

            if (count($errors) > 0) {
                $errorMessages = [];

                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }

                return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
            }

            $searchRequest = $searchRequestFactory->create($indexType, $searchIndexRequest);
            $response = $client->search($searchRequest);

            return new JsonResponse($response);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
