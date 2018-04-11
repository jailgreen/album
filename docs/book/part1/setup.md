# Setup the application

The easiest way to get started with Expressive is to use the [skeleton application and installer](https://github.com/zendframework/zend-expressive-skeleton).
The skeleton provides a generic structure for creating your applications, and prompts you to choose a router, dependency injection container, template renderer, and error handler from the outset.

## Create a new project

First, we'll create a new project, using [Composer](https://getcomposer.org/)'s `create-project` command:

````bash
$ composer create-project zendframework/zend-expressive-skeleton expressive-album
````

- [] step-by-step installation
- [x] Finish my changes
- [ ] Push my commits to GitHub
- [ ] Open a pull request

Once complete, enter the project directory:

````bash
$ cd zend-expressive-album
````

You can now startup PHP's [built-in web server](http://php.net/manual/en/features.commandline.webserver.php); the Expressive skeleton provides a short-cut for it via Composer:

````bash
$ composer serve
````

This starts up a web server on localhost port 8080; browse to [http://localhost:8080/](http://localhost:8080/) to see if your application responds correctly!

![Expressive Start Page](../images/screen-after-installation.png)