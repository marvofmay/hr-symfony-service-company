# OpenAPIClient-php

No description provided (generated by Openapi Generator https://github.com/openapitools/openapi-generator)


## Installation & Usage

### Requirements

PHP 7.4 and later.
Should also work with PHP 8.0.

### Composer

To install the bindings via [Composer](https://getcomposer.org/), add the following to `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/GIT_USER_ID/GIT_REPO_ID.git"
    }
  ],
  "require": {
    "GIT_USER_ID/GIT_REPO_ID": "*@dev"
  }
}
```

Then run `composer install`

### Manual Installation

Download the files and include `autoload.php`:

```php
<?php
require_once('/path/to/OpenAPIClient-php/vendor/autoload.php');
```

## Getting Started

Please follow the [installation procedure](#installation--usage) and then run the following:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');




$apiInstance = new OpenAPI\Client\Api\LOGINApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$api_login_post_request = new \OpenAPI\Client\Model\ApiLoginPostRequest(); // \OpenAPI\Client\Model\ApiLoginPostRequest

try {
    $result = $apiInstance->apiLoginPost($api_login_post_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling LOGINApi->apiLoginPost: ', $e->getMessage(), PHP_EOL;
}

```

## API Endpoints

All URIs are relative to *http://127.0.0.1:81*

Class | Method | HTTP request | Description
------------ | ------------- | ------------- | -------------
*LOGINApi* | [**apiLoginPost**](docs/Api/LOGINApi.md#apiloginpost) | **POST** /api/login | Logowanie
*RolesApi* | [**apiRolesGet**](docs/Api/RolesApi.md#apirolesget) | **GET** /api/roles | Pobiera listę ról
*RolesApi* | [**apiRolesPost**](docs/Api/RolesApi.md#apirolespost) | **POST** /api/roles | Dodaje rolę

## Models

- [ApiLoginPost200Response](docs/Model/ApiLoginPost200Response.md)
- [ApiLoginPost401Response](docs/Model/ApiLoginPost401Response.md)
- [ApiLoginPostRequest](docs/Model/ApiLoginPostRequest.md)
- [ApiRolesGet200Response](docs/Model/ApiRolesGet200Response.md)
- [ApiRolesGet200ResponseData](docs/Model/ApiRolesGet200ResponseData.md)
- [ApiRolesGet200ResponseDataItemsInner](docs/Model/ApiRolesGet200ResponseDataItemsInner.md)
- [ApiRolesPost201Response](docs/Model/ApiRolesPost201Response.md)
- [ApiRolesPost422Response](docs/Model/ApiRolesPost422Response.md)
- [ApiRolesPost500Response](docs/Model/ApiRolesPost500Response.md)
- [ApiRolesPostRequest](docs/Model/ApiRolesPostRequest.md)

## Authorization

Authentication schemes defined for the API:
### BearerAuth

- **Type**: Bearer authentication (JWT)

## Tests

To run the tests, use:

```bash
composer install
vendor/bin/phpunit
```

## Author



## About this package

This PHP package is automatically generated by the [OpenAPI Generator](https://openapi-generator.tech) project:

- API version: `0.1.0`
    - Generator version: `7.7.0`
- Build package: `org.openapitools.codegen.languages.PhpClientCodegen`
