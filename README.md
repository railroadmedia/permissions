# Role and permission package

This package it's an API that allows admin to manage access rules and user access in a database and protects routes with the package middleware.

## Installation

Require the package in your composer.json and update your dependency with composer update.

## API Reference

###endpoints

Prepend all endpoints below with '/permissions'.

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
| name\*           | string    |    x      |           |
| slug\*           | string    |    x      |           |
| description      | string    |           |     x     |
| brand            | string    |           |     x     |



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




