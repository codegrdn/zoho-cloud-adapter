---
title: API Reference

language_tabs:
- bash
- javascript

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.
[Get Postman Collection](http://woo-to-inventory/docs/collection.json)

<!-- END_INFO -->

#general
<!-- START_7ba029714012cd9c08cc50ae4dee9d7a -->
## Authenticate
Authenticate a user and return the token if the provided credentials are correct

> Example request:

```bash
curl -X POST "/api/auth/login" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"email":"totam","password":"aut"}'

```
```javascript
const url = new URL("/api/auth/login");

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "email": "totam",
    "password": "aut"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response (200):

```json
{
    "Status": 0,
    "token": "eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NSIsIm5hbWUiOiJKb2huIEdvbGQiLCJhZG1pbiI6dHJ1ZX0.LIHjWCBORSWMEibq-tnT8ue_deUqZx1K0XxCOXZRrBI"
}
```
> Example response (400):

```json
{
    "Status": 1,
    "Msg": "Email does not exist."
}
```
> Example response (400):

```json
{
    "Status": 1,
    "Msg": "Email or password is wrong."
}
```

### HTTP Request
`POST /api/auth/login`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    email | string |  required  | 
    password | string |  required  | 

<!-- END_7ba029714012cd9c08cc50ae4dee9d7a -->

<!-- START_40c1a3c327efaa7eb146d563ce3ea45c -->
## Register user in portal

> Example request:

```bash
curl -X POST "/api/auth/register_user" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"reg_token":"suscipit","name":"omnis","email":"et","password":"aspernatur","settings":"consequuntur","create_potential":false,"create_account":false,"create_products":false,"send_errors":false,"extraFieldsMapping":"blanditiis"}'

```
```javascript
const url = new URL("/api/auth/register_user");

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "reg_token": "suscipit",
    "name": "omnis",
    "email": "et",
    "password": "aspernatur",
    "settings": "consequuntur",
    "create_potential": false,
    "create_account": false,
    "create_products": false,
    "send_errors": false,
    "extraFieldsMapping": "blanditiis"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response (200):

```json
{}
```
> Example response (400):

```json
{}
```
> Example response (400):

```json
{}
```
> Example response (400):

```json
{}
```
> Example response (400):

```json
{}
```
> Example response (400):

```json
{}
```
> Example response (400):

```json
{}
```

### HTTP Request
`POST /api/auth/register_user`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    reg_token | string |  required  | token to match with env val
    name | string |  required  | 
    email | string |  required  | 
    password | string |  required  | 
    settings | json |  required  | {"zoho_inventory_key": "", "zoho_inventory_organization_id": "", "zoho_crm_client_id": "", "zoho_crm_secret": "", "zoho_crm_grant_token": "", "zoho_crm_email": "", "zoho_crm_redirect_uri": ""} set both inventory and crm credentials or one of them
    create_potential | boolean |  optional  | 
    create_account | boolean |  optional  | 
    create_products | boolean |  optional  | 
    send_errors | boolean |  optional  | 
    extraFieldsMapping | json |  optional  | mapping for create/update order (see this actions doc for json) actions to set values from meta_data or set them as predefined value for Contacts, Deals, Accounts, SalesOrders modules {"Contacts":{"Assistant":"$some constant$","Asst_Phone":"_aftership_tracking_number"}}

<!-- END_40c1a3c327efaa7eb146d563ce3ea45c -->

<!-- START_edd8e4c37fdc2c88c8070f934306bbd1 -->
## Sale order create
Create queue record that creates sale order and contact/account/deal/products in system that user setted up in registration

> Example request:

```bash
curl -X POST "/api/order/created" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"token":"molestiae","json":"iure"}'

```
```javascript
const url = new URL("/api/order/created");

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "token": "molestiae",
    "json": "iure"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response (200):

```json
{
    "Status": true
}
```
> Example response (200):

```json
{
    "Status": false
}
```

### HTTP Request
`POST /api/order/created`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    token | string |  required  | users jwt token
    json | json |  required  | json from woo commerce with data

<!-- END_edd8e4c37fdc2c88c8070f934306bbd1 -->

<!-- START_d362a72ac4df45db3fe5056122db0b43 -->
## Sale order update
Craete queue record that updates sale order products in system that user setted up in registration

> Example request:

```bash
curl -X POST "/api/order/updated" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"token":"dolorem","json":"facilis"}'

```
```javascript
const url = new URL("/api/order/updated");

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "token": "dolorem",
    "json": "facilis"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response (200):

```json
{
    "Status": true
}
```
> Example response (200):

```json
{
    "Status": false
}
```

### HTTP Request
`POST /api/order/updated`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    token | string |  required  | users jwt token
    json | json |  required  | json from woo commerce with data

<!-- END_d362a72ac4df45db3fe5056122db0b43 -->

<!-- START_81d6364ea95358acb4d22389f1a7cda7 -->
## Change status

> Example request:

```bash
curl -X POST "/api/changeStatusLead" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"change_token":"tempore","id":17,"Status":"fuga"}'

```
```javascript
const url = new URL("/api/changeStatusLead");

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "change_token": "tempore",
    "id": 17,
    "Status": "fuga"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response (200):

```json
{}
```
> Example response (400):

```json
{}
```

### HTTP Request
`POST /api/changeStatusLead`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    change_token | string |  required  | 
    id | integer |  required  | 
    Status | string |  required  | Duplicated; Converted; FTD; Not Relevant

<!-- END_81d6364ea95358acb4d22389f1a7cda7 -->


