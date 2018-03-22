# Role and permission package

This package it's an API that allows admin to manage access rules and user access in a database and protects routes with the package middleware.

## Installation

Require the package in your composer.json and update your dependency with composer update.

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
| access_id        | integer   |    x      |           |



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


------------------------------------------------------------------------------------------------------------------------

### create access hierarchy

**PUT** "user-access"

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
        "access_id": 1,
        "user_id": 21,
        "created_on": "2018-03-22 08:33:55",
        "updated_on": null
    }
}
```


------------------------------------------------------------------------------------------------------------------------

### delete access hierarchy

**DELETE** "user-access"

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


------------------------------------------------------------------------------------------------------------------------





