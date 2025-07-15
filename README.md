# 🔎 Symfony Search Bundle

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/Symfony-000000?style=for-the-badge&logo=Symfony&logoColor=white)
![Elasticsearch](https://img.shields.io/badge/elasticsearch-%230377CC.svg?style=for-the-badge&logo=elasticsearch&logoColor=white)

This Symfony bundle provides a fast and developer-friendly way to create and manage Elasticsearch indexes, populate them with documents, and perform powerful searches — all in just a few minutes.

With minimal setup, you only need to define two PHP classes per index. From there, everything else is handled for you:

- Ready-to-use endpoints for indexing, updating, deleting, and retrieving documents

- A robust search API for querying your data

- Optional usage of provided ClientInterface services to perform these actions programmatically

No prior knowledge of Elasticsearch is required. The bundle acts as a high-level abstraction layer over Elasticsearch, making all interactions seamless and transparent.

## Features
- 🔧 Zero-configuration search engine integration
- 🧱 Index definition via two simple PHP classes
- 📦 Automatic route generation for all operations
- 🧪 Built-in validation before indexation
- 🧰 Optional service interfaces (`IndexClientInterface`, `DocumentClientInterface`, `SearchClientInterface`)
- 🔍 Full-text search with filtering, sorting, pagination, and aggregations
- 📉 No Elasticsearch knowledge required — it's fully abstracted
- 🎛️ Easy to implement your own custom filters

# Contents

- [Installation](#installation)
- [Creating your index](#creating-your-index)
- [Adding index to Elasticsearch](#adding-index-to-elasticsearch)
- [Indexing a single document](#indexing-a-single-document)
- [Updating a single document](#updating-a-single-document)
- [Indexing multiple documents](#indexing-multiple-documents)
- [Updating multiple documents](#updating-multiple-documents)
- [Getting a document](#getting-a-document)
- [Deleting a document](#deleting-a-document)
- [Search for documents](#search-for-documents)
- [Retrieving Filterable Fields](#retrieving-filterable-fields)
- [Retrieving Sortable Fields](#retrieving-sortable-fields)

# Installation

### 1. Require the bundle

```bash
composer require adriballa/symfony-search-bundle
```

### 2. Load Routes

Add the following to `config/routes/adriballa.yaml`:

```yaml
adriballa:
  resource:
    path: '@AdriballaSymfonySearchBundle/src/Controller/'
    namespace: Adriballa\SymfonySearchBundle\Controller
  type: attribute
```

## 3. Connect the bundle to Elasticsearch

Create a config file at `config/packages/adriballa_symfony_search.yaml` to provide the `dsn` of your elasticsearch instance.

```yaml
adriballa_symfony_search:
  elastic_dsn: 'https://elastic:elastic@localhost?SSLVerification=1'
```

# Creating your index

## Creating index definition and mapping

To define a new index, create two PHP classes that implements these interfaces :

- `IndexDefinitionInterface`: declares metadata (name, scope, limits)
- `IndexMappingInterface`: declares the structure and fields of the index

Index management is fully automated by the Symfony Search bundle.

**Example of a creation of a `users` index.

```php
class UsersIndexDefinition implements IndexDefinitionInterface
{
    // The type of your index, will be used for naming and routing
    public static function getIndexType(): string
    {
        return 'users';
    }

    // Settings and mapping of your index
    public function getIndexMapping(): IndexMappingInterface
    {
        return new UsersIndexMapping();
    }

    // Visibility of your index, only public index can be searched
    public function getScope(): ?IndexScope
    {
        return IndexScope::Public;
    }

    // Max pagination limit
    public function getPaginationLimit(): int
    {
        return 10000;
    }
}
```


```php
class UsersIndexMapping implements IndexMappingInterface
{
    use DefaultDynamicTemplates;

    // Settings of your index, define the number of replicas, shards and refresh interval
    public function getIndexSettings(): IndexSettings
    {
        return new IndexSettings(2, 1, 15);
    }

    // Define any raw explicit mapping, can remain blank
    public function getExplicitMapping(): array
    {
        return [];
    }

    // Define the mapping of the index with an array of FieldDefinitionInterface
    /**
    * @return FieldDefinitionInterface[]
    */
    public function getFields(): array
    {
        return [
            new KeywordField('id'),
            new SearchableKeywordField('login'),
            new SearchableKeywordField(path: 'role'),
            new SearchableTextField('description'),
            new BooleanField('active', searchable: true),
            new DateField('starting_date', sortable: true),
            new GeoPointField('location'),
            new ObjectField(
                path: 'monitoring',
                properties: [
                    new LongField('ping'),
                    new LongField('score'),
                    new LongField('reviews'),
                ]),
        ];
    }
}
```

# Adding index to Elasticsearch

When your `IndexDefinitionInterface` is ready, you can automatically create your index in Elasticsearch using either the Symfony Search Bundle route or the method of the `IndexClient`. 

## Via API

**Endpoint**: `/indexes`  
**Method**: `POST`  
**Request Body (`application/json`)**

| Parameter        | Type    | Required | Description                                                  |
| ---------------- | ------- | -------- | ------------------------------------------------------------ |
| `indexType`      | string  | Yes      | The type of index to create, as defined in `IndexDefinition` |
| `addAlias`       | boolean | No       | Whether to assign an alias to the newly created index        |
| `deleteExisting` | boolean | No       | Whether to delete any existing index of the same type        |

**Success response**

```json
{
  "type": "users",                        // Your indexType as defined in IndexDefinition
  "name": "index-users-20250626092802"    // The actual name of the index in Elasticsearch
}
```

**Error response**
```json
{
  "error": "Index definition for type wrong_index_type not found"
}
```

## Via PHP

You can also use `IndexClientInterface` as a dependency injection in your project and use it to add your index into Elasticsearch.

```php
$indexType = 'users'                        // IndexType of your index as set in your IndexDefinitionInterface
$addAlias = true                            // Should this index be set as Alias (optional, usually true)
$deleteExisting = true                      // Should the previous index set as alias be deleted (false by default, usually true)

$indexClient->createIndex($indexType, $addAlias, $deleteExisting);
```

# Indexing a single document

Every indexation and updates of document will be passing through a document validation that will detected if the provided data is matching the `IndexDefinitionMapping` that is set for this index.

## Via API

You can index a single document to Elasticsearch using the Symfony Search Bundle API.
The body of this `POST` would be the json encoded data of your document.

**Endpoint**: `/indexes/{indexType}/documents/{id}`  
**Method**: `POST`  
**Request Body (`application/json`)**

| Parameter | Type          | Required | Description                                       |
| --------- | ------------- | -------- | ------------------------------------------------- |
| `id`      | string (path) | Yes      | The document ID                                   |
| Body      | JSON          | Yes      | JSON object matching the index mapping definition |

**Success response**
```json
{
  "success": true,
  "errors": []
}
```
**Error response**
```json
{
  "success": false,
  "errors": {
    "[id]": "This value should be of type string.",
    "[location][lat]": "This field is missing."
  }
}
```

## via PHP

You can also use `DocumentClientInterface` as a dependency injection in your project and use it to index a document in your Elasticsearch index.

```php
$index = new Index('users');
$document = new Document(
            'user-1', // id of your document
            [
                'id' => 'user-1'
                'login' => 'user-1@mail.com'
                // ... json encoded data of your document
            ]
        );

$documentClient->indexDocument($index, $document);
```

Indexing a document with the `DocumentClientInterface` will return a `IndexationResponse`.

# Updating a single document

Every indexation and updates of document will be passing through a document validation that will detected if the provided data is matching the `IndexDefinitionMapping` that is set for this index.

## Via API

You can update a single document to Elasticsearch using the Symfony Search Bundle API.
The body of this `POST` would be the json encoded data of your document.

**Endpoint**: `/indexes/{indexType}/documents/{id}`  
**Method**: `PUT`  
**Request Body (`application/json`)**

| Parameter | Type          | Required | Description                                       |
| --------- | ------------- | -------- | ------------------------------------------------- |
| `id`      | string (path) | Yes      | The document ID                                   |
| Body      | JSON          | Yes      | JSON object matching the index mapping definition |

**Success response**
```json
{
  "success": true,
  "errors": []
}
```
**Error response**
```json
{
  "success": false,
  "errors": {
    "[id]": "This value should be of type string.",
    "[location][lat]": "This field is missing."
  }
}
```

## via PHP

You can also use `DocumentClientInterface` as a dependency injection in your project and use it to update a document in your Elasticsearch index.

```php
$index = new Index('users');
$document = new Document(
            'user-1', // id of your document
            [
                'id' => 'user-1'
                'login' => 'user-1@mail.com'
                // ... json encoded data of your document
            ]
        );

$documentClient->updateDocument($index, $document);
```

Indexing a document with the `DocumentClientInterface` will return a `IndexationResponse`.

# Indexing multiple documents

Every indexation and updates of document will be passing through a document validation that will detected if the provided data is matching the `IndexDefinitionMapping` that is set for this index.

## Via API

You can index multiple documents to Elasticsearch using the Symfony Search Bundle API.
The body of this `POST` would be an array of the json encoded data of your document.

**Endpoint**: `/indexes/{indexType}/documents/`  
**Method**: `POST`  
**Request Body (`application/json`)**

| Parameter | Type | Required | Description                                           |
| --------- | ---- | -------- | ----------------------------------------------------- |
| Body      | JSON | Yes      | An array of JSON documents matching the index mapping |

**Response**
```json
{
  "total": 20,        // Number of documents to insert
  "failure": 1,       // Number of failures 
  "errors": {         // If there are errors, they are listed here (document id serves as key)
    "1001": {
      "[id]": "This value should be of type string."
    }
  }
}
```

## via PHP

You can also use `DocumentClientInterface` as a dependency injection in your project and use it to index a document in your Elasticsearch index.

```php
$index = new Index('users');
$document = new Document(
            'user-1', // id of your document
            [
                'id' => 'user-1'
                'login' => 'user-1@mail.com'
                // ... json encoded data of your document
            ]
        );

$documentClient->mIndexDocuments($index, [$document]);
```

Indexing multiple documents with the `DocumentClientInterface` will return a `MultipleIndexationResponse`.

# Updating multiple documents

Every indexation and updates of document will be passing through a document validation that will detected if the provided data is matching the `IndexDefinitionMapping` that is set for this index.

## Via API

You can update multiple documents to Elasticsearch using the Symfony Search Bundle API.
The body of this `POST` would be the json encoded data of your document.

**Endpoint**: `/indexes/{indexType}/documents`  
**Method**: `PUT`  
**Request Body (`application/json`)**

| Parameter | Type | Required | Description                                           |
| --------- | ---- | -------- | ----------------------------------------------------- |
| Body      | JSON | Yes      | An array of JSON documents matching the index mapping |

**Response**  
```json
{
  "total": 20,        // Number of documents to insert
  "failure": 1,       // Number of failures 
  "errors": {         // If there are errors, they are listed here (document id serves as key)
    "1001": {
      "[id]": "This value should be of type string."
    }
  }
}
```

## Via PHP

You can also use `DocumentClientInterface` as a dependency injection in your project and use it to update a document in your Elasticsearch index.

```php
$index = new Index('users');
$document = new Document(
            'user-1', // id of your document
            [
                'id' => 'user-1'
                'login' => 'user-1@mail.com'
                // ... json encoded data of your document
            ]
        );

$documentClient->mUpdateDocument($index, [$document]);
```

Updating multiple documents with the `DocumentClientInterface` will return a `MultipleIndexationResponse`.

# Getting a document

## Via API

You can get a document from your Elasticsearch index using the Symfony Search Bundle API.

**Endpoint**: `/indexes/{indexType}/documents/{id}`  
**Method**: `GET`  

| Parameter | Type          | Required | Description                     |
| --------- | ------------- | -------- | ------------------------------- |
| `id`      | string (path) | Yes      | The ID of the document to fetch |

**Success Response**  
The response of this endpoint when successful will be a json encoded version of your document

**Error Response**  
If the document or the index is missing, this endpoint will return a `404 Not Found`.

## Via PHP

You can also use `DocumentClientInterface` as a dependency injection in your project and use it to get a document in your Elasticsearch index.

```php
$index = new Index('users');
$id = 'user-1';

$document = $documentClient->getDocument($index, $id);
```

The `DocumentClientInterface` will return a `Document` object when fetching a document.

# Deleting a document

## Via API

You can delete a document from your Elasticsearch index using the Symfony Search Bundle API.

**Endpoint**: `/indexes/{indexType}/documents/{id}`  
**Method**: `DELETE`

**Success Response**  
If the document is found and deleted, the status code of the response will be `204 No Content`.

**Error Response**  
If the document or the index is missing, this endpoint will return a 404.

## Via PHP

You can also use `DocumentClientInterface` as a dependency injection in your project and use it to get a document in your Elasticsearch index.

```php
$index = new Index('users');
$id = 'user-1';

$document = $documentClient->deleteDocument($index, $id); // Returns true if the document is deleted, false otherwise
```

# Search for documents

You can search documents in a given index using the Symfony Search Bundle API.

## Via API

**Endpoint**: `/indexes/{indexType}/search`  
**Method**: `GET`  
**Query Parameters**

| Parameter       | Type     | Required | Description                                                                 |
|----------------|----------|----------|-----------------------------------------------------------------------------|
| `query`         | string   | No       | The search term or query string                                             |
| `searchFields`  | array    | No       | List of fields to target for full-text search (can be repeated in query)   |
| `start`         | integer  | No       | Starting offset for pagination (default: 0)                                |
| `size`          | integer  | No       | Number of results to return (default: 10)                                  |
| `filtersBy`     | array    | No       | Key-value pairs for filtering documents (field => value(s))                |
| `aggregatesBy`  | array    | No       | Fields to aggregate on (for facets, counts, etc.)                          |
| `sortsBy`       | array    | No       | Sort configuration (e.g., field => ASC/DESC)                  

**Example of query**
```
localhost/indexes/users/search?start=0&sortsBy[]=starting_date DESC&size=10&filtersBy["active"]=true&filtersBy["monitoring.score"]=100..200&aggregatesBy[]=job_title
```

**Success response**
```json
{
  "indexType": "users",
  "success": true,
  "start": 0,
  "size": 10,
  "duration": 23,
  "totalHits": 2,
  "hits": [
    {
      "id": "user-1",
      "login": "admin@site.com"
    },
    {
      "id": "user-2",
      "login": "admin2@site.com"
    }
  ],
  "aggregations": {
    "role": {
      "admin": 2,
      "editor": 0
    }
  }
}
```

## Via PHP

You can search documents in a given index using the `SearchClientInterface` as a dependency injection in your project.

```php
$index = new Index('users');
$query = 'john.doe@example.com'; // Optional query string

$searchRequest = new SearchRequest(
    index: $index,
    queryString: 'text to search',                  //Optional : query to search for
    range: new Range(0,10), 
    fieldsToSearch: ['email', 'name'],              // Optional : Fields to look for the query, every field will be search if not set
    fieldsToFetch: ['email', 'name', 'created_at'], // Optional : fields to return, everything will be return if not set
    aggregations: [                                 // Optional: your AggregationInterface[] objects
        new Aggregation('job_title'),
    ],                               
    filters: [                                      // Optional: your FilterableInterface[] objects
        new ExactMatch('active',[true]),    
    ],                                    
    sorts: [                                         // Optional: Sort objects for ordering
        new Sort('starting_date',SortDirection::DESC)
    ]                                      
);

$response = $searchClient->search($searchRequest);
```

The `search` method of the `SearchClientInterface` returns a `SearchResponse`.

# Retrieving Filterable Fields

You can retrieve the list of fields that are marked as filterable for a given index.

**Endpoint:** /indexes/{indexType}/filters  
**Method:** GET

| Parameter   | Type          | Required | Description                      |
| ----------- | ------------- | -------- | -------------------------------- |
| `indexType` | string (path) | Yes      | The type of the index to inspect |

**Success response**
```json
  [
    "active",
    "monitoring.score",
    "role"
  ]
```

**Error response**

If the index definition is not found, the API will return a `204 No Content`.

# Retrieving Sortables Fields

You can retrieve the list of fields that are marked as sortable for a given index.

**Endpoint:** /indexes/{indexType}/sorts  
**Method:** GET

| Parameter   | Type          | Required | Description                      |
| ----------- | ------------- | -------- | -------------------------------- |
| `indexType` | string (path) | Yes      | The type of the index to inspect |

**Success response**
```json
  [
    "starting_date",
    "monitoring.score"
  ]
```

**Error response**

If the index definition is not found, the API will return a `204 No Content`.
