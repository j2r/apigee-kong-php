#Notes
The Apigee to scripts are developed in PHP. 

Ideal PHP version expected is php-7.*. 

Note : These scripts are to kick start the migration and this should run on local/dev environment of Kong first.
 
#Steps
  - Make a copy of `config/default.apigee.yml` to `config/apigee.yml`. Provide Apigee credentials in `config/apigee.yml` file.
  
  - Make a copy of `config/default.kong.yml` to `config/kong.yml`. Provide Kong credentials in `config/kong.yml` file.
  
  - Go to `src` directory and run `php migrate.php`.
  
#Process
- Apigee provide management API and that is being used to export Apigee proxies.
- Get the list of Apigee proxies.
- Get the last revision of those proxies.
- Download and extract the proxy information.
- Push targets to services. 
- Push mapped proxies to routes.
- Check the policies attached to the proxies (As of now the code support Quota and Key-Authentication proxy migration) and migrate those to plugins.
- Get the list of Apps from consumer along with developer ID and client secret, migrate those to consumer with the same key mapped to support key based authentication.
 
#Extra
- Storing of proxy information in yml is not required but storing there could help you for cross verification.
- The `src/kongCleanup.php` will cleanup/delete all the routes, services and Consumer from Kong. This is just for debugging and kind or temporary rollback.
