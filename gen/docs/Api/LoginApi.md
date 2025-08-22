# OpenAPI\Client\LoginApi

All URIs are relative to http://127.0.0.1:81, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**apiLoginPost()**](LoginApi.md#apiLoginPost) | **POST** /api/login | Logowanie |


## `apiLoginPost()`

```php
apiLoginPost($api_login_post_request): \OpenAPI\Client\Model\ApiLoginPost200Response
```

Logowanie

Umożliwia autentykację.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new OpenAPI\Client\Api\LoginApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$api_login_post_request = new \OpenAPI\Client\Model\ApiLoginPostRequest(); // \OpenAPI\Client\Model\ApiLoginPostRequest

try {
    $result = $apiInstance->apiLoginPost($api_login_post_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling LoginApi->apiLoginPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **api_login_post_request** | [**\OpenAPI\Client\Model\ApiLoginPostRequest**](../Model/ApiLoginPostRequest.md)|  | |

### Return type

[**\OpenAPI\Client\Model\ApiLoginPost200Response**](../Model/ApiLoginPost200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
