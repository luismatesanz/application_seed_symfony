Symfony Seed Application
=====================

Project Seed Symfony 3 starter for WEB + API + ADMIN. 

With Bundles:
- FOSRestBundle
- FOSUserBundle
- JMSSerializer
- NelmioAPIDocBundle
- NelmioCorsBundle
- LexikJWTAuthenticationBundle
- EasyAdminBundle

API with JWT Authentication, messages only JSON and Serializer filter fields.

With BlogBundle example bundles associates to project.


Installation
------------

- Generate the SSH keys for JWT Token:

        $ mkdir -p var/jwt 
        $ openssl genrsa -out var/jwt/private.pem -aes256 4096
        $ openssl rsa -pubout -in var/jwt/private.pem -out var/jwt/public.pem

- Execute in command line in path folder project:

        1. symfony install
        2. bin/console doctrine:create:database
        3. bin/console doctrine:schema:update --force --dump-sql
        4. bin/console assets:install --symlink
        5. bin/console fos:user:create testuser test@example.com p@ssword
       
Documentation
-------------

AppBundle is a bundle by default application configure.

You must create bundle by each entity in your domain model with the same structure in BlogBundle. You can add logic by bundle to Web, API and Admin.

The layer Handler is your folder for bussiness logic.


Routes
-------------

- / is a route for web application.
- /admin is a route for admin application.
- /api is a route for api application.
- /api/doc is a route for your api swagger documentation.


API
-------------

- You can get only fields you want with query param fields. Example:

        /api/blogs?fields=date,title