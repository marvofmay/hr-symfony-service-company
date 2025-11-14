# OpenAPI\Client\RolesApi

All URIs are relative to http://127.0.0.1:81, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**apiRolesGet()**](RolesApi.md#apiRolesGet) | **GET** /api/roles | Pobiera listę ról |
| [**apiRolesImportPost()**](RolesApi.md#apiRolesImportPost) | **POST** /api/roles/import | Importuje role z pliku |
| [**apiRolesMultipleDelete()**](RolesApi.md#apiRolesMultipleDelete) | **DELETE** /api/roles/multiple | Usuwa wskazane role |
| [**apiRolesPost()**](RolesApi.md#apiRolesPost) | **POST** /api/roles | Dodaje rolę |
| [**apiRolesUuidAccessesPost()**](RolesApi.md#apiRolesUuidAccessesPost) | **POST** /api/roles/{uuid}/accesses | Dodaje dostępy dla roli |
| [**apiRolesUuidAcessesPermissionsPost()**](RolesApi.md#apiRolesUuidAcessesPermissionsPost) | **POST** /api/roles/{uuid}/acesses/permissions | Dodaje pozwolenia dostępu dla roli |
| [**apiRolesUuidDelete()**](RolesApi.md#apiRolesUuidDelete) | **DELETE** /api/roles/{uuid} | Usuwa wskazana rolę |
| [**apiRolesUuidGet()**](RolesApi.md#apiRolesUuidGet) | **GET** /api/roles/{uuid} | Pobiera wskazaną rolę |
| [**apiRolesUuidPut()**](RolesApi.md#apiRolesUuidPut) | **PUT** /api/roles/{uuid} | Aktualizuje wskazaną rolę |


## `apiRolesGet()`

```php
apiRolesGet($page, $page_size, $sort_by, $sort_direction, $deleted, $phrase, $includes): \OpenAPI\Client\Model\ApiRolesGet200Response
```

Pobiera listę ról

Pozwala na pobranie listy ról

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\RolesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$page = 1; // int | Numer strony (paginacja)
$page_size = 10; // int | Liczba elementów na stronę
$sort_by = name; // string | Pole do sortowania (np. name)
$sort_direction = desc; // string | Kierunek sortowania
$deleted = 0; // int | Filtr usuniętych rekordów (0 - aktywne, 1 - usunięte)
$phrase = user; // string | Wyszukiwana fraza
$includes = employees; // string | Relacje do załadowania (np. employees)

try {
    $result = $apiInstance->apiRolesGet($page, $page_size, $sort_by, $sort_direction, $deleted, $phrase, $includes);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling RolesApi->apiRolesGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **page** | **int**| Numer strony (paginacja) | [optional] [default to 1] |
| **page_size** | **int**| Liczba elementów na stronę | [optional] [default to 10] |
| **sort_by** | **string**| Pole do sortowania (np. name) | [optional] |
| **sort_direction** | **string**| Kierunek sortowania | [optional] |
| **deleted** | **int**| Filtr usuniętych rekordów (0 - aktywne, 1 - usunięte) | [optional] |
| **phrase** | **string**| Wyszukiwana fraza | [optional] |
| **includes** | **string**| Relacje do załadowania (np. employees) | [optional] |

### Return type

[**\OpenAPI\Client\Model\ApiRolesGet200Response**](../Model/ApiRolesGet200Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiRolesImportPost()`

```php
apiRolesImportPost($file): \OpenAPI\Client\Model\ApiRolesImportPost201Response
```

Importuje role z pliku

Pozwala na import ról z pliku Excel (.xlsx). Wymagana kolumna 'name' (min 3, max 50 znaków). Opcjonalnie 'description'.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\RolesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$file = "/path/to/file.txt"; // \SplFileObject | Plik Excel (.xlsx) z kolumnami: name (wymagane), description (opcjonalne)

try {
    $result = $apiInstance->apiRolesImportPost($file);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling RolesApi->apiRolesImportPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **file** | **\SplFileObject****\SplFileObject**| Plik Excel (.xlsx) z kolumnami: name (wymagane), description (opcjonalne) | [optional] |

### Return type

[**\OpenAPI\Client\Model\ApiRolesImportPost201Response**](../Model/ApiRolesImportPost201Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: `multipart/form-data`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiRolesMultipleDelete()`

```php
apiRolesMultipleDelete($api_roles_multiple_delete_request): \OpenAPI\Client\Model\ApiRolesMultipleDelete200Response
```

Usuwa wskazane role

Pozwala na usunięcie wskazanych ról

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\RolesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$api_roles_multiple_delete_request = new \OpenAPI\Client\Model\ApiRolesMultipleDeleteRequest(); // \OpenAPI\Client\Model\ApiRolesMultipleDeleteRequest

try {
    $result = $apiInstance->apiRolesMultipleDelete($api_roles_multiple_delete_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling RolesApi->apiRolesMultipleDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **api_roles_multiple_delete_request** | [**\OpenAPI\Client\Model\ApiRolesMultipleDeleteRequest**](../Model/ApiRolesMultipleDeleteRequest.md)|  | |

### Return type

[**\OpenAPI\Client\Model\ApiRolesMultipleDelete200Response**](../Model/ApiRolesMultipleDelete200Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiRolesPost()`

```php
apiRolesPost($api_roles_post_request): \OpenAPI\Client\Model\ApiRolesPost201Response
```

Dodaje rolę

Pozwala na dodanie roli

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\RolesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$api_roles_post_request = new \OpenAPI\Client\Model\ApiRolesPostRequest(); // \OpenAPI\Client\Model\ApiRolesPostRequest

try {
    $result = $apiInstance->apiRolesPost($api_roles_post_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling RolesApi->apiRolesPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **api_roles_post_request** | [**\OpenAPI\Client\Model\ApiRolesPostRequest**](../Model/ApiRolesPostRequest.md)|  | |

### Return type

[**\OpenAPI\Client\Model\ApiRolesPost201Response**](../Model/ApiRolesPost201Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiRolesUuidAccessesPost()`

```php
apiRolesUuidAccessesPost($api_roles_uuid_accesses_post_request): \OpenAPI\Client\Model\ApiRolesUuidAccessesPost201Response
```

Dodaje dostępy dla roli

Pozwala dodać wybrane dostepy dla danej roli

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\RolesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$api_roles_uuid_accesses_post_request = new \OpenAPI\Client\Model\ApiRolesUuidAccessesPostRequest(); // \OpenAPI\Client\Model\ApiRolesUuidAccessesPostRequest

try {
    $result = $apiInstance->apiRolesUuidAccessesPost($api_roles_uuid_accesses_post_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling RolesApi->apiRolesUuidAccessesPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **api_roles_uuid_accesses_post_request** | [**\OpenAPI\Client\Model\ApiRolesUuidAccessesPostRequest**](../Model/ApiRolesUuidAccessesPostRequest.md)|  | |

### Return type

[**\OpenAPI\Client\Model\ApiRolesUuidAccessesPost201Response**](../Model/ApiRolesUuidAccessesPost201Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiRolesUuidAcessesPermissionsPost()`

```php
apiRolesUuidAcessesPermissionsPost($api_roles_uuid_acesses_permissions_post_request): \OpenAPI\Client\Model\ApiRolesUuidAcessesPermissionsPost201Response
```

Dodaje pozwolenia dostępu dla roli

Pozwala dodać wybrane pozwolenia dostepu dla danej roli

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\RolesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$api_roles_uuid_acesses_permissions_post_request = new \OpenAPI\Client\Model\ApiRolesUuidAcessesPermissionsPostRequest(); // \OpenAPI\Client\Model\ApiRolesUuidAcessesPermissionsPostRequest

try {
    $result = $apiInstance->apiRolesUuidAcessesPermissionsPost($api_roles_uuid_acesses_permissions_post_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling RolesApi->apiRolesUuidAcessesPermissionsPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **api_roles_uuid_acesses_permissions_post_request** | [**\OpenAPI\Client\Model\ApiRolesUuidAcessesPermissionsPostRequest**](../Model/ApiRolesUuidAcessesPermissionsPostRequest.md)|  | |

### Return type

[**\OpenAPI\Client\Model\ApiRolesUuidAcessesPermissionsPost201Response**](../Model/ApiRolesUuidAcessesPermissionsPost201Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiRolesUuidDelete()`

```php
apiRolesUuidDelete($uuid): \OpenAPI\Client\Model\ApiRolesUuidDelete200Response
```

Usuwa wskazana rolę

Pozwala na usunięcie wskazanej roli

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\RolesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$uuid = 45df2733-666f-42dc-90d2-a76a177bab1d; // string | UUID roli

try {
    $result = $apiInstance->apiRolesUuidDelete($uuid);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling RolesApi->apiRolesUuidDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **uuid** | **string**| UUID roli | |

### Return type

[**\OpenAPI\Client\Model\ApiRolesUuidDelete200Response**](../Model/ApiRolesUuidDelete200Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiRolesUuidGet()`

```php
apiRolesUuidGet($uuid): \OpenAPI\Client\Model\ApiRolesUuidGet200Response
```

Pobiera wskazaną rolę

Pozwala na pobranie wskazanej roli

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\RolesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$uuid = 45df2733-666f-42dc-90d2-a76a177bab1d; // string | UUID roli

try {
    $result = $apiInstance->apiRolesUuidGet($uuid);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling RolesApi->apiRolesUuidGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **uuid** | **string**| UUID roli | |

### Return type

[**\OpenAPI\Client\Model\ApiRolesUuidGet200Response**](../Model/ApiRolesUuidGet200Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiRolesUuidPut()`

```php
apiRolesUuidPut($api_roles_uuid_put_request): \OpenAPI\Client\Model\ApiRolesUuidPut201Response
```

Aktualizuje wskazaną rolę

Pozwala na aktualizację wskazanej roli

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: BearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\RolesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$api_roles_uuid_put_request = new \OpenAPI\Client\Model\ApiRolesUuidPutRequest(); // \OpenAPI\Client\Model\ApiRolesUuidPutRequest

try {
    $result = $apiInstance->apiRolesUuidPut($api_roles_uuid_put_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling RolesApi->apiRolesUuidPut: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **api_roles_uuid_put_request** | [**\OpenAPI\Client\Model\ApiRolesUuidPutRequest**](../Model/ApiRolesUuidPutRequest.md)|  | |

### Return type

[**\OpenAPI\Client\Model\ApiRolesUuidPut201Response**](../Model/ApiRolesUuidPut201Response.md)

### Authorization

[BearerAuth](../../README.md#BearerAuth)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
