# Role and permission package

[![Build Status](https://travis-ci.com/railroadmedia/permissions.svg?branch=master)](https://travis-ci.com/railroadmedia/permissions)

This package it's an API that allows admin to manage access rules and user access in a database and protects routes with the package middleware.

## Installation

Require the package in your composer.json and update your dependency with composer update.

``
 "railroad/permissions" : "1.0.*"
``

## API Reference

### endpoints

Prepend all endpoints below with '/permissions'.
Anything in curly braces is an inline parameter.

List of methods available
------------------------------------------------------------------------------------------------------------------------

* create access
* update access
* delete access
* assign user access
* revoke user access
* create access inheriting
* delete access inheriting

Methods
------------------------------------------------------------------------------------------------------------------------

### create access

**PUT** "access"

| param            | data-type | required  | optional  |
|------------------|-----------|-----------|-----------|
| name\*           | string    |     x     |           |
| slug\*           | string    |     x     |           |
| description      | string    |           |      x    |
| brand            | string    |           |      x    |



#### Returns, on success

* status code `200`
* status  `ok`
* `results` array

```json
{
    "status":"ok",
    "code":200,
    "results":{
        "id": 1,
        "name": "edit content",
        "slug": "edit-content",
        "description": "Can edit CMS content",
        "brand": "drumeo",
        "created_on": "2018-03-22 08:11:54",
        "updated_on": null
    }
}
```


------------------------------------------------------------------------------------------------------------------------

### update access

**PATCH** "access/{accessId}"

| param            | data-type | required  | optional  |
|------------------|-----------|-----------|-----------|
| name             | string    |           |      x    |
| slug             | string    |           |      x    |
| description      | string    |           |      x    |
| brand            | string    |           |      x    |



#### Returns, on success

* status code `201`
* status  `ok`
* `results` array

```json
{
    "status":"ok",
    "code":201,
    "results":{
        "id": 1,
        "name": "edit content modified",
        "slug": "edit-content",
        "description": "Can edit CMS content",
        "brand": "drumeo",
        "created_on": "2018-03-22 08:11:54",
        "updated_on": "2018-03-22 08:27:13"
    }
}
```

#### Returns, when the access not exist

* status code `404`
* status  `error`
* `error` array

```json
{
    "status":"error",
    "code":404,
    "total_results":0,
    "results":{},
    "error":{
        "title": "Not found.",
        "detail": "Update failed, access not found with id: {accessId}"
    }
}
```
------------------------------------------------------------------------------------------------------------------------

### delete access

**DELETE** "access/{accessId}"


#### Returns, on success

* status code `204`
* status  `ok`
* `results` null

```json
{
    "status":"ok",
    "code":204,
    "results": null
}
```

#### Returns, when the access not exist

* status code `404`
* status  `error`
* `error` array

```json
{
    "status":"error",
    "code":404,
    "total_results":0,
    "results":{},
    "error":{
        "title": "Not found.",
        "detail": "Delete failed, access not found with id: {accessId}"
    }
}
```

------------------------------------------------------------------------------------------------------------------------

### assign user access

**PUT** "user-access"

| param            | data-type | required  | optional  |
|------------------|-----------|-----------|-----------|
| user_id          | integer   |    x      |           |
| access_id        | integer   |    x      |           |



#### Returns, on success

* status code `200`
* status  `ok`
* `results` array

```json
{
    "status":"ok",
    "code":200,
    "results":{
        "id": 1,
        "access_id": 1,
        "user_id": 21,
        "created_on": "2018-03-22 08:33:55",
        "updated_on": null
    }
}
```


------------------------------------------------------------------------------------------------------------------------

### revoke user access

**DELETE** "user-access"

| param            | data-type | required  | optional  |
|------------------|-----------|-----------|-----------|
| user_id          | integer   |    x      |           |
| access_slug      |  string   |    x      |           |



#### Returns, on success

* status code `204`
* status  `ok`
* `results` null

```json
{
    "status":"ok",
    "code":204,
    "results": null
}
```

#### Returns, when the user access not exist

* status code `404`
* status  `error`
* `error` array

```json
{
    "status":"error",
    "code":404,
    "total_results":0,
    "results":{},
    "error":{
        "title": "Not found.",
        "detail": "Delete failed, user have not access to: {accessSlug}"
    }
}
```

------------------------------------------------------------------------------------------------------------------------

### create access hierarchy

**PUT** "access-hierarchy"

| param            | data-type | required  | optional  |
|------------------|-----------|-----------|-----------|
| parent_id        | integer   |    x      |           |
| child_id         | integer   |    x      |           |



#### Returns, on success

* status code `200`
* status  `ok`
* `results` array

```json
{
    "status":"ok",
    "code":200,
    "results":{
        "id": 1,
        "parent_id": 1,
        "child_id": 21,
        "created_on": "2018-03-22 08:33:55",
        "updated_on": null
    }
}
```


------------------------------------------------------------------------------------------------------------------------

### delete access hierarchy

**DELETE** "access-hierarchy"

| param            | data-type | required  | optional  |
|------------------|-----------|-----------|-----------|
| parent_id        | integer   |    x      |           |
| child_id         | integer   |    x      |           |



#### Returns, on success

* status code `204`
* status  `ok`
* `results` null

```json
{
    "status":"ok",
    "code":204,
    "results": null
}
```

#### Returns, when the access hierarchy not exist

* status code `404`
* status  `error`
* `error` array

```json
{
    "status":"error",
    "code":404,
    "total_results":0,
    "results":{},
    "error":{
        "title": "Not found.",
        "detail": "Delete failed, access have not the child:  {childId}"
    }
}
```

------------------------------------------------------------------------------------------------------------------------


Validation Errors
------------------------------------------------------------------------------------------------------------------------

The package use Form Request Validation classes that contain validation logic and the incoming form request is validated before the controller method is called. 

All the validation errors are available in the JSON response's ```errors``` array.

Example:

```json
{
 "status":"error",
 "code": 422,
  "total_results": 0,
  "results":{},
  "errors":{
    "0": {
      "source":"name",
      "detail":"The name field is required."
    },
    "1":{
      "source":"slug",
      "detail":"The slug field is required."
    }
  }
}  
```

------------------------------------------------------------------------------------------------------------------------


Protecting Routes using middleware
------------------------------------------------------------------------------------------------------------------------

The package comes with PermissionMiddleware middleware that it's registered automatically as 'permission'. With the middleware you can easly filter your router by user access rights.

In order to protect a route you have to specify the 'permission' middleware on the route you'd like to protect and specify the access rules slugs as an array. 

Example:

```
  Route::patch(
            '/address/{id}',
            [
                'uses' => Railroad\Ecommerce\Controllers\AddressJsonController::class . '@update',
                'middleware' => ['permission'],
                'permissions' => ['admin','editor',isOwner'],
            ]
        )->name('address.update');
```

When the route is requested it will check if the currently logged in user has ``admin`` or the ``editor`` role or it's the `owner` of the given address. 

If the user has access to the given resource then the controller will be called as normal.

If there is not logged in user and the route should be protected an error it's returned in the JSON response's ```errors``` array:

```json
{
 "status":"error",
 "code": 403,
  "total_results": 0,
  "results":{},
  "error":{
      "title":"Not allowed.",
      "detail":"This action is unauthorized. Please login"  
  }
}  
```

If the logged in user have not access to the protected route an error it's returned in the JSON response's ```errors``` array:

```json
{
 "status":"error",
 "code": 403,
  "total_results": 0,
  "results":{},
  "error":{
      "title":"Not allowed.",
      "detail":"This action is unauthorized."  
  }
}  
```

### Ownership verification


The concept of ownership is used to allow users to perform actions on resources they 'own'. 
In order to protect a route for ownership you have to specify the 'permission' middleware on the route you'd like to protect and specify the **'isOwner'** permission.

It's ``required`` to create the configuration file in order to use the package middleware `isOwner` permission.
 
The configuration file should be located in the config directory and should contain:
* an associative array with the route name and table name (with key *table_names*). This array should contain the mapping between route names and table names for all the routes that are protected with **isOwner** permission. There are some situations when for a route name we should check the ownership in different table based on the parameters send on the request: e.g.: On e-commerce project we have user and customer. If we want to update a payment method we should verify the user or the customer ownership. In the configuration file we have the possibility to define an array for the route table name; the keys should be the request parameters name ('user_id' or 'customer_id') and the value should be the table name that should be used (*'ecommerce_user_payment_methods'* or *'ecommerce_customer_payment_methods'*)
* an associative array with the route name and the primary key that should be checked (with key `column_names`). By default the package will check against `id` primary key. You do not have to pass in all column names; only the ones that are not ``id``.

Example:

````php
return [
    'table_names' => [
        'address.update' => 'ecommerce_address',
        'address.delete' => 'ecommerce_address',
        'payment-method.update' => [
            'user_id'     => 'ecommerce_user_payment_methods',
            'customer_id' => 'ecommerce_customer_payment_methods'
        ],
        'payment-method.delete' => 'ecommerce_user_payment_methods',	
    ],
    'column_names' => [
        'payment-method.update' => 'payment_method_id',
        'payment-method.delete' => 'payment_method_id',
    ]
];
````    

