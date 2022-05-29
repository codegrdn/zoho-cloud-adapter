# Granalixs woo commerce to inventory and crm

sale order data in json parsed and stored in zoho crm or zoho invenory

  - register user to store zoho services api access data
  - login user to get auth token
  - crate queue record via url request
  - with json data
    - create orders, with contact and possible deal account and products
    - update orders and create products if not found

### INSTALL
To install:

    * clone repo
    * composer install
    * copy .env.example
    * set env vars for database
    * set other env variables
    * php artisan migrate
    * to setup google drive:
    
1.  https://developers.google.com/drive/api/v3/quickstart/js
2. enable the drive api
3. edit the oauth and add the redirect uri
after user is created go here 
https://cloudconn.customize.co.il/auth/google-drive?user_id=6


to create user with dropbox
dropbox  storage token
http://dropboxdev.customize.co.il/api/auth/register?reg_token=123&email=test%40demo2.holisticcrm.co.il&name=test&password=test&platform_type=dropbox&storage_token=sl.AnaojptickIi7jCLGWbzxoaZ5nlRQ2YiJB46Rok5o18EszK4vwhRShpDDGI8fb-D5u6SQ5qE_XuUVtipZqobZn1qFOegZp0ywGiCydUNPuyrjmWdl5pnZHP4A76vexKX2OzzjKcIxTw






    
#### SET ENV VARS
    APP_KEY= set here string with 32symbols length
    MAIL_DRIVER=smtp
    MAIL_HOST=mail service host
    MAIL_PORT=mail service port
    MAIL_USERNAME=mail service user name
    MAIL_PASSWORD=mail service password
    MAIL_ENCRYPTION=mail service enc type
    MAIL_FROM_ADDRESS=email that will be in from address in recievers mail
    MAIL_FROM_NAME=name that will be in from address in recievers mail

    QUEUE_CONNECTION=sync or database

    JWT_SECRET= type some value
    REG_TOKEN= value that must present in registration request
    THROTTLE_NUM= amount of requests in minute for user
	

## API Examples

### User :: Create
```shell
curl --request POST \
  --url 'http://dropboxdev.customize.co.il/api/auth/register?reg_token=123&email=projects%40holisticcrm.co.il&name=projects2&password=projects2&platform_type=onedrive'
```

### OndeDrive :: Sync
```shell
curl --request GET \
--url 'http://dropboxdev.customize.co.il/api/sync-onedrive?module=Leads&id=4554416000002381015&token=f9852d1db184cbcd01cb034a003e225c&directory=d1%2Fd2%2Fd3&='
```
