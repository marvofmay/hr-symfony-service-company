# OpenAPI\Client\NotesApi

All URIs are relative to http://127.0.0.1:81, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**apiNotesGet()**](NotesApi.md#apiNotesGet) | **GET** /api/notes | Pobiera listę notatek |
| [**apiNotesPost()**](NotesApi.md#apiNotesPost) | **POST** /api/notes | Dodaje notatkę |
| [**apiNotesUuidDelete()**](NotesApi.md#apiNotesUuidDelete) | **DELETE** /api/notes/{uuid} | Usuwa wskazana rolę |
| [**apiNotesUuidGet()**](NotesApi.md#apiNotesUuidGet) | **GET** /api/notes/{uuid} | Pobiera wskazaną notatkę |
| [**apiNotesUuidPut()**](NotesApi.md#apiNotesUuidPut) | **PUT** /api/notes/{uuid} | Aktualizuje wskazaną notatkę |


## `apiNotesGet()`

```php
apiNotesGet($page, $page_size, $sort_by, $sort_direction, $deleted, $phrase, $includes): \OpenAPI\Client\Model\ApiNotesGet200Response
```

Pobiera listę notatek

Pozwala na pobranie listy notatek

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\NotesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$page = 1; // int | Numer strony (paginacja)
$page_size = 10; // int | Liczba elementów na stronę
$sort_by = name; // string | Pole do sortowania (np. title)
$sort_direction = desc; // string | Kierunek sortowania
$deleted = 0; // int | Filtr usuniętych rekordów (0 - aktywne, 1 - usunięte)
$phrase = user; // string | Wyszukiwana fraza
$includes = employee; // string | Relacje do załadowania (np. employee)

try {
    $result = $apiInstance->apiNotesGet($page, $page_size, $sort_by, $sort_direction, $deleted, $phrase, $includes);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling NotesApi->apiNotesGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **page** | **int**| Numer strony (paginacja) | [optional] [default to 1] |
| **page_size** | **int**| Liczba elementów na stronę | [optional] [default to 10] |
| **sort_by** | **string**| Pole do sortowania (np. title) | [optional] |
| **sort_direction** | **string**| Kierunek sortowania | [optional] |
| **deleted** | **int**| Filtr usuniętych rekordów (0 - aktywne, 1 - usunięte) | [optional] |
| **phrase** | **string**| Wyszukiwana fraza | [optional] |
| **includes** | **string**| Relacje do załadowania (np. employee) | [optional] |

### Return type

[**\OpenAPI\Client\Model\ApiNotesGet200Response**](../Model/ApiNotesGet200Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiNotesPost()`

```php
apiNotesPost($api_notes_post_request): \OpenAPI\Client\Model\ApiNotesPost201Response
```

Dodaje notatkę

Pozwala na dodanie notatki

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\NotesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$api_notes_post_request = new \OpenAPI\Client\Model\ApiNotesPostRequest(); // \OpenAPI\Client\Model\ApiNotesPostRequest

try {
    $result = $apiInstance->apiNotesPost($api_notes_post_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling NotesApi->apiNotesPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **api_notes_post_request** | [**\OpenAPI\Client\Model\ApiNotesPostRequest**](../Model/ApiNotesPostRequest.md)|  | |

### Return type

[**\OpenAPI\Client\Model\ApiNotesPost201Response**](../Model/ApiNotesPost201Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiNotesUuidDelete()`

```php
apiNotesUuidDelete($uuid): \OpenAPI\Client\Model\ApiNotesUuidDelete200Response
```

Usuwa wskazana rolę

Pozwala na usunięcie wskazanej roli

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\NotesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$uuid = 45df2733-666f-42dc-90d2-a76a177bab1d; // string | UUID roli

try {
    $result = $apiInstance->apiNotesUuidDelete($uuid);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling NotesApi->apiNotesUuidDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **uuid** | **string**| UUID roli | |

### Return type

[**\OpenAPI\Client\Model\ApiNotesUuidDelete200Response**](../Model/ApiNotesUuidDelete200Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiNotesUuidGet()`

```php
apiNotesUuidGet($uuid): \OpenAPI\Client\Model\ApiNotesUuidGet200Response
```

Pobiera wskazaną notatkę

Pozwala na pobranie wskazanej notatki

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\NotesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$uuid = 45df2733-666f-42dc-90d2-a76a177bab1d; // string | UUID notatki

try {
    $result = $apiInstance->apiNotesUuidGet($uuid);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling NotesApi->apiNotesUuidGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **uuid** | **string**| UUID notatki | |

### Return type

[**\OpenAPI\Client\Model\ApiNotesUuidGet200Response**](../Model/ApiNotesUuidGet200Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiNotesUuidPut()`

```php
apiNotesUuidPut($api_notes_post_request): \OpenAPI\Client\Model\ApiNotesUuidPut201Response
```

Aktualizuje wskazaną notatkę

Pozwala na aktualizację wskazanej notatki

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\NotesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$api_notes_post_request = new \OpenAPI\Client\Model\ApiNotesPostRequest(); // \OpenAPI\Client\Model\ApiNotesPostRequest

try {
    $result = $apiInstance->apiNotesUuidPut($api_notes_post_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling NotesApi->apiNotesUuidPut: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **api_notes_post_request** | [**\OpenAPI\Client\Model\ApiNotesPostRequest**](../Model/ApiNotesPostRequest.md)|  | |

### Return type

[**\OpenAPI\Client\Model\ApiNotesUuidPut201Response**](../Model/ApiNotesUuidPut201Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
