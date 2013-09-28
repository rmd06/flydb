Flydb 
=====

This is a simple fly (*Drosophila*) management web application, 
written to easy laboratory fly stock management and sharing.

It is a project using Symfony2 by a novice web developer (also a young 
biologist, coincidentally), as his first web project, partly for fun.
So, although it is ready to use :), no test has been written so far :(

1) Get the Flydb codes
----------------------

Download the codes. For example, using `git`

    git clone https://github.com/singingstars/flydb.git flydb-or-whatever-folder-name
    cd flydb-or-whatever-folder-name

2) Install dependencies 
-----------------------

### Get `composer`

    curl -s http://getcomposer.org/installer | php

### Install vendor libaries

    php composer.phar update

### Install [elastic search][4]

Refer to [this note][5] for Ubuntu 12.04 or [elastic search installation][6]
After that, probably you need `php-curl`

    sudo apt-get install php5-curl

3) Setup `Symfony2`
-------------------

It's easier to setup a basic Symfony2 project, and then change the web root
to flydb's `web/`
Refer to the [Symfony2 documentation on installation][1]

Use command `php app/check.php` to check the setup

Don't forget to modify permissions

    rm -rf app/cache/*
    rm -rf app/logs/*

    sudo setfacl -R -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs
    sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs

4) Setup database and (optionally) prepare sample data
------------------------------------------------------

Inside the app,

    cp app/config/parameters.yml.dist app/config/parameters.yml

and do config according to Symfony2 [docs][2], then update database schema

    php app/console doctrine:database:create
    php app/console doctrine:schema:update --force

Optionally, some test data is ready for import (do not do this when data
already exists in your database)

    php app/console doctrine:fixtures:load
    
Also, might need to populate existing data for the search module to work

    php app/console foq:elastica:populate
    
5) Done.
--------

Should return to you a nice web page when accessed from a browser.

Of course, the look is greatly copied from [Twig website][3]
and the codes, a little messy.

Also, a backend management for users and locations has yet to be setup.
Hopefully I will have time to make the improvements.


[1]:  http://symfony.com/doc/2.1/book/installation.html
[2]:  http://symfony.com/doc/2.1/book/doctrine.html
[3]:  http://twig.sensiolabs.org/
[4]:  http://www.elasticsearch.org/
[5]:  https://gist.github.com/2026107
[6]:  http://www.elasticsearch.org/guide/reference/setup/installation.html
