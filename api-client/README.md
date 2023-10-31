# OpenAPIClient-php

This is an awesome app!


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




$apiInstance = new OpenAPI\Client\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$user_id = 135; // string | ID пользователя
$count = 5; // string | Количество твитов в ленте

try {
    $apiInstance->getFeed($user_id, $count);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->getFeed: ', $e->getMessage(), PHP_EOL;
}

```

## API Endpoints

All URIs are relative to *http://localhost*

Class | Method | HTTP request | Description
------------ | ------------- | ------------- | -------------
*DefaultApi* | [**getFeed**](docs/Api/DefaultApi.md#getfeed) | **GET** /api/v1/get-feed | 
*DefaultApi* | [**apiDocAreaGet**](docs/Api/DefaultApi.md#apidocareaget) | **GET** /api/doc/{area} | 
*DefaultApi* | [**apiV1AddFollowersAsyncPushOneMessagePost**](docs/Api/DefaultApi.md#apiv1addfollowersasyncpushonemessagepost) | **POST** /api/v1/add-followers-async-push-one-message | 
*DefaultApi* | [**apiV1AddFollowersPost**](docs/Api/DefaultApi.md#apiv1addfollowerspost) | **POST** /api/v1/add-followers | 
*DefaultApi* | [**apiV1AddFollowersSimplePost**](docs/Api/DefaultApi.md#apiv1addfollowerssimplepost) | **POST** /api/v1/add-followers-simple | 
*DefaultApi* | [**apiV1DataPost**](docs/Api/DefaultApi.md#apiv1datapost) | **POST** /api/v1/data | 
*DefaultApi* | [**apiV1GetUsersByQueryGet**](docs/Api/DefaultApi.md#apiv1getusersbyqueryget) | **GET** /api/v1/get-users-by-query | 
*DefaultApi* | [**apiV1GetUsersByQueryWithAggregationGet**](docs/Api/DefaultApi.md#apiv1getusersbyquerywithaggregationget) | **GET** /api/v1/get-users-by-query-with-aggregation | 
*DefaultApi* | [**apiV1GetUsersWithAggregationGet**](docs/Api/DefaultApi.md#apiv1getuserswithaggregationget) | **GET** /api/v1/get-users-with-aggregation | 
*DefaultApi* | [**apiV1TokenPost**](docs/Api/DefaultApi.md#apiv1tokenpost) | **POST** /api/v1/token | 
*DefaultApi* | [**apiV1TweetGet**](docs/Api/DefaultApi.md#apiv1tweetget) | **GET** /api/v1/tweet | 
*DefaultApi* | [**apiV1TweetPost**](docs/Api/DefaultApi.md#apiv1tweetpost) | **POST** /api/v1/tweet | 
*DefaultApi* | [**apiV1UploadPost**](docs/Api/DefaultApi.md#apiv1uploadpost) | **POST** /api/v1/upload | 
*DefaultApi* | [**apiV1UserAsyncPost**](docs/Api/DefaultApi.md#apiv1userasyncpost) | **POST** /api/v1/user/async | 
*DefaultApi* | [**apiV1UserFormGet**](docs/Api/DefaultApi.md#apiv1userformget) | **GET** /api/v1/user/form | 
*DefaultApi* | [**apiV1UserFormIdGet**](docs/Api/DefaultApi.md#apiv1userformidget) | **GET** /api/v1/user/form/{id} | 
*DefaultApi* | [**apiV1UserFormIdPatch**](docs/Api/DefaultApi.md#apiv1userformidpatch) | **PATCH** /api/v1/user/form/{id} | 
*DefaultApi* | [**apiV1UserFormPost**](docs/Api/DefaultApi.md#apiv1userformpost) | **POST** /api/v1/user/form | 
*DefaultApi* | [**apiV1UserGet**](docs/Api/DefaultApi.md#apiv1userget) | **GET** /api/v1/user | 
*DefaultApi* | [**apiV1UserIdDelete**](docs/Api/DefaultApi.md#apiv1useriddelete) | **DELETE** /api/v1/user/{id} | 
*DefaultApi* | [**apiV1UserPatch**](docs/Api/DefaultApi.md#apiv1userpatch) | **PATCH** /api/v1/user | 
*DefaultApi* | [**apiV1UserPost**](docs/Api/DefaultApi.md#apiv1userpost) | **POST** /api/v1/user | 
*DefaultApi* | [**apiV2UserByLoginUserLoginGet**](docs/Api/DefaultApi.md#apiv2userbyloginuserloginget) | **GET** /api/v2/user/by-login/{user_login} | 
*DefaultApi* | [**apiV2UserCustomHeaderGet**](docs/Api/DefaultApi.md#apiv2usercustomheaderget) | **GET** /api/v2/user/customHeader | 
*DefaultApi* | [**apiV2UserFormGet**](docs/Api/DefaultApi.md#apiv2userformget) | **GET** /api/v2/user/form | 
*DefaultApi* | [**apiV2UserFormIdGet**](docs/Api/DefaultApi.md#apiv2userformidget) | **GET** /api/v2/user/form/{id} | 
*DefaultApi* | [**apiV2UserFormIdPatch**](docs/Api/DefaultApi.md#apiv2userformidpatch) | **PATCH** /api/v2/user/form/{id} | 
*DefaultApi* | [**apiV2UserFormPost**](docs/Api/DefaultApi.md#apiv2userformpost) | **POST** /api/v2/user/form | 
*DefaultApi* | [**apiV2UserGet**](docs/Api/DefaultApi.md#apiv2userget) | **GET** /api/v2/user | 
*DefaultApi* | [**apiV2UserPatch**](docs/Api/DefaultApi.md#apiv2userpatch) | **PATCH** /api/v2/user | 
*DefaultApi* | [**apiV2UserPost**](docs/Api/DefaultApi.md#apiv2userpost) | **POST** /api/v2/user | 
*DefaultApi* | [**apiV2UserUserIdDelete**](docs/Api/DefaultApi.md#apiv2useruseriddelete) | **DELETE** /api/v2/user/{user_id} | 
*DefaultApi* | [**apiV3UserDelete**](docs/Api/DefaultApi.md#apiv3userdelete) | **DELETE** /api/v3/user | 
*DefaultApi* | [**apiV3UserGet**](docs/Api/DefaultApi.md#apiv3userget) | **GET** /api/v3/user | 
*DefaultApi* | [**apiV3UserPatch**](docs/Api/DefaultApi.md#apiv3userpatch) | **PATCH** /api/v3/user | 
*DefaultApi* | [**apiV3UserPost**](docs/Api/DefaultApi.md#apiv3userpost) | **POST** /api/v3/user | 
*DefaultApi* | [**apiV4GetUsersGet**](docs/Api/DefaultApi.md#apiv4getusersget) | **GET** /api/v4/get-users | 
*DefaultApi* | [**apiV4SaveUserPost**](docs/Api/DefaultApi.md#apiv4saveuserpost) | **POST** /api/v4/save-user | 
*DefaultApi* | [**apiV5SaveUserPost**](docs/Api/DefaultApi.md#apiv5saveuserpost) | **POST** /api/v5/save-user | 

## Models


## Authorization
Endpoints do not require authorization.

## Tests

To run the tests, use:

```bash
composer install
vendor/bin/phpunit
```

## Author



## About this package

This PHP package is automatically generated by the [OpenAPI Generator](https://openapi-generator.tech) project:

- API version: `1.0.0`
- Build package: `org.openapitools.codegen.languages.PhpClientCodegen`
