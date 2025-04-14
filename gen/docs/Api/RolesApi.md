# OpenAPI\Client\RolesApi

All URIs are relative to http://127.0.0.1:81, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**apiRolesGet()**](RolesApi.md#apiRolesGet) | **GET** /api/roles | Pobiera listę ról |
| [**apiRolesPost()**](RolesApi.md#apiRolesPost) | **POST** /api/roles | Dodaje rolę |


## `apiRolesGet()`

```php
apiRolesGet(): \OpenAPI\Client\Model\ApiRolesGet200Response
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

try {
    $result = $apiInstance->apiRolesGet();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling RolesApi->apiRolesGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

This endpoint does not need any parameter.

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
