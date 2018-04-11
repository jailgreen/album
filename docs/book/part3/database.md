# Setup the database and the database connection

In this part of the tutorial, we will setup a database and implement the model layer for our application. At the end of this chapter, the album list page will show the data from the database.

First, we need to setup the database. We will use MySQL for this tutorial.

Create a new database called `album-tutorial`, and then run the following SQL statements to create the `albums` table, along with some test data.

````sql
CREATE TABLE albums (
    id int(11) NOT NULL auto_increment,
    artist varchar(100) NOT NULL,
    title varchar(100) NOT NULL,
    PRIMARY KEY (id)
);
INSERT INTO albums (artist, title)
    VALUES  ('The  Military  Wives',  'In  My  Dreams');
INSERT INTO albums (artist, title)
    VALUES  ('Adele',  '21');
INSERT INTO albums (artist, title)
    VALUES  ('Bruce  Springsteen',  'Wrecking Ball (Deluxe)');
INSERT INTO albums (artist, title)
    VALUES  ('Lana  Del  Rey',  'Born  To  Die');
INSERT INTO albums (artist, title)
    VALUES  ('Gotye',  'Making  Mirrors');
````

## Install Doctrine component

Next, we'll add the `doctrine` component to the application using [composer](https://getcomposer.org)

````bash
$composer require dasprid/container-interop-doctrine
````
### Configuration

Edit `config/autoload/dependencies.global.php` and add in the dependencies factory section following:

````php
return [
     ...
    'dependencies' => [
        ...
        'factories' => [
            'doctrine.entity_manager.orm_default' => \ContainerInteropDoctrine\EntityManagerFactory::class,
        ],
    ],
    ...
];
````
Add configuration to `App\ConfigProvider`:

````php
/**
     * Returns doctrine configuration
     * @return array
     */
    public function getDoctrine() : array
    {
        return [
            'connection' => [
                'orm_default' => [
                    'params' => [
                        'url' => 'mysql://jukka:jukka_db@localhost/jukka_db',
                    ],
                ],
            ],
            'driver' => [
                'orm_default' => [
                    'class' => \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain::class,
                    'drivers' => [
                        'App\Model\Entity' => 'app_entity',
                    ],
                ],
                'app_entity' => [
                    'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => __DIR__ . '/Model/Entity',
                ],
            ],
        ];
    }
````
Create file `config/cli-config.php`:

````php
<?php
$container = require 'container.php';

$entityManager = $container->get('doctrine.entity_manager.orm_default');
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
````

At this point, we have setup the database, and provided a database adapter to our application.

## Create an album entity

To represent the data of the albums, we will create an entity class. 
Create the directory `Model\Entity`; under it, create an `AlbumEntity.php` file with the following contents:

````php
<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity @ORM\Table(name="albums")
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
class AlbumEntity
{
    /**
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $artist;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $title;

    /**
     *
     * @return string
     */
    public function getArtist() : string
    {
        return $this->artist;
    }

    /**
     *
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     *
     * @param string $artist
     * @return void
     */
    public function setArtist(string $artist) : void
    {
        $this->artist = $artist;
    }

    /**
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title) : void
    {
        $this->title = $title;
    }

    /**
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

}
````

## Create a storage interface

To access the data from the database table, we will use the `Doctrine` component. But before we do that, we have a little preparation to do first.

If we use `Doctrine\EntityManager` directly, we're binding our model to a specific data access layer, and more generally to relational databases. 
This means that if any changes happen to the `Doctrine` implementation, we will need to change our code; 
if we decide to move to a NoSQL database later, we will need to change our code.

To prevent the need for such changes, we will create a storage interface modeling our low layer data access needs.

Create the path `src/Album/Model/Storage/` and then create the file `AlbumStorageInterface.php` beneath it. This interface 
defines methods for reading a list of albums, reading a single album, inserting an album, updating an album, and deleting albums.

````php
<?php

declare (strict_types=1);

namespace App\Model\Storage;

use App\Model\Entity\AlbumEntity;

/**
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
interface StorageInterface
{
    /**
     * Fetch a list of albums.
     *
     * @return array AlbumEntity[]
     */
    public function fetchAlbumList();
}
````

Any time you want to use a different data storage — e.g., a NoSQL database, a web service, etc. — you can create a new class implementing this interface. You would then only need to swap which storage implementation you use.

## Implement the storage interface

Because storage is not part of the domain model and implementation can be swapped (because we defined a storage interface!), we'll place our table gateway in a separate path. 
Create the directory `src/Album/Db/`, and place the `ORMStorage.php` file in it. Our storage will implement the `AlbumStorageInterface` interface defined in the previous section.

````php
<?php

declare(strict_types=1);

namespace App\Db;

use App\Model\Entity\AlbumEntity;
use App\Model\Storage\StorageInterface;
use Doctrine\ORM\EntityManager;

/**
 * Description of ORMStorage
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
class ORMStorage implements StorageInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchAlbumList(): array
    {
        $albums = $this->entityManager
                ->getRepository(AlbumEntity::class)
                ->findAll();

        return $albums;
    }
}
````

Please note that all of these methods either return an `AlbumEntity` or an array collection of `AlbumEntity` instances,
 and, if any parameters are accepted, they typically only accept an `AlbumEntity` instance (with the exception of `fetchAlbumById()`).
 There is no need to pass arrays to the command methods or to handle arrays returned passed from the query methods.

To get our `ORMStorage` configured properly, we will also need a factory in the same path. 
The `ORMStorageFactory` requests the instance of the `entityManager` via the service container (zend-servicemanager in our case)-
It is then injected into the constructor of the `ORMStorage`.

````php
<?php

declare(strict_types=1);

namespace App\Db;

use Psr\Container\ContainerInterface;

/**
 * Description of ORMStorageFactory
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
class ORMStorageFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new ORMStorage($container->get('doctrine.entity_manager.orm_default'));
    }
}
````

## Create an album repository

When creating our domain model, we need something to mediate between the domain objects — our entities — and the storage layer. This is generally achieved by a [repository](http://martinfowler.com/eaaCatalog/repository.html).

A repository accepts and returns domain objects, and decides whether or not storage operations are necessary. Often, they will cache results in order to reduce overhead on subsequent requests to the same methods, though this is not a strict requirement.

We'll now create a repository for the album. The repository will be used within our middleware actions, and consume an `AlbumStorageInterface` implementation as developed in the previous section. This will allow us to switch from a database to a web service, or to use a different implementation than the table gateway, without needing to change any application code.

As with storage, we'll start by creating an interface. Create the directory `src/Album/Model/Repository/` and place the file `AlbumRepositoryInterface.php` it. This interface is similar to the `AlbumStorageInterface`, but combines insert and update operations into a single "save" method.

````php
<?php

declare(strict_types=1);

use App\Model\Entity\AlbumEntity;

/**
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
interface AlbumRepositoryInterface
{
    /**
     * Fetch all albums.
     * 
     * @return array AlbumEntity[]
     */
    public function fetchAlbumList();
}
````

This may seem like duplication of effort, as many methods are duplicated between this and the `AlbumStorageInterface`. However, the separation provides a number of benefits:

- Separation of concerns. We can add entity validation, caching, etc. as part of the repository, keeping them separate from storage.
- If our middleware consumes only the repository, we can mock the repository during testing to validate behavior.

Let's create a reference implementation of the interface as well. In the same path, create the file `AlbumRepository.php`. The class it defines will implement the `AlbumRepositoryInterface`, and compose an `AlbumStorageInterface` instance, provided to the constructor.

````php
<?php

declare(strict_types=1);

use App\Model\Entity\AlbumEntity;
use App\Model\Storage\StorageInterface;

/**
 * Description of AlbumRepository
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
class AlbumRepository implements AlbumRepositoryInterface
{
    /**
     *
     * @var StorageInterface
     */
    private $albumStorage;

    /**
     * AlbumRepository constructor.
     *
     * @param StorageInterface $albumStorage
     */
    public function __construct(StorageInterface $albumStorage)
    {
        $this->albumStorage = $albumStorage;
    }

    /**
     * 
     * {@inheritDoc}
     */
    public function fetchAlbumList(): array
    {
        return $this->albumStorage->fetchAlbumList();
    }
}
````

Most methods of this class proxy directly to the appropriate methods of the storage; only the `saveAlbum()` method does any extra work (to determine whether an insert or update operation is warranted).

The `AlbumRepository` needs a factory. Create the file `AlbumRepositoryFactory.php` within the same directory; in this factory, we'll request the album storage from the service container, and pass it to the constructor of the repository.

````php
<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Storage\StorageInterface;
use Psr\Container\ContainerInterface;

/**
 * Description of AlbumRepositoryFactory
 *
 * @author jailgreen <36865973+jailgreen@users.noreply.github.com>
 */
class AlbumRepositoryFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new AlbumRepository($container->get(StorageInterface::class));
    }
}
````

## Update the album configuration

Now that we have storage and our repository sorted, we need to add dependency configuration to the application.
 Edit the file `ConfigProvider.php` and add the following configuration to the `dependencies` section.

````php
<?php
return [
    /* ... */
        'factories' => [
            /* ... */
            Model\Repository\AlbumRepositoryInterface::class =>
                    Model\Repository\AlbumRepositoryFactory::class,

            Model\Storage\StorageInterface::class =>
                Db\ORMStorageFactory::class,
        ],
    ],
    
    /* ... */
];
````

For both the repository and the storage we use the interface names as the identifier and the factories for the instantiation.

## Update the album list middleware

Now that we have our domain models, repository, and storage created, we can update our middleware to use them.

Edit the file src/App/Handler/AlbumListHandler.php and implement the following changes:

````php
<?php

declare(strict_types=1);

namespace App\Handler;

use App\Model\Repository\AlbumRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class AlbumListHandler implements RequestHandlerInterface
{
    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    /**
     *
     * @var AlbumRepositoryInterface
     */
    private $albumRepository;

    public function __construct(
        TemplateRendererInterface $renderer,
        AlbumRepositoryInterface $albumRepository
    ){
        $this->renderer        = $renderer;
        $this->albumRepository = $albumRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $data = [
            'albumList' => $this->albumRepository->fetchAlbumList(),
        ];

        return new HtmlResponse($this->renderer->render(
            'app::album-list',
            $data
        ));
    }
}
````

The changes in the above include:

- Adding another private property, `$albumRepository`, to hold an `AlbumRepositoryInterface` instance.
- Changing the constructor to add a second parameter, `$albumRepository`, accepting an `AlbumRepositoryInterface` instance and assigning it to the `$albumRepository` property.
- Filling the `$data` array within the `handle()` method with a list of albums fetched from the repository.

Because we've added a new constructor argument, we will need to update the `AlbumListHandlerFactory`:

````php
<?php

declare(strict_types=1);

namespace App\Handler;

use App\Model\Repository\AlbumRepositoryInterface;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class AlbumListHandlerFactory
{
    public function __invoke(ContainerInterface $container) : AlbumListHandler
    {
        return new AlbumListHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(AlbumRepositoryInterface::class)
        );
    }
}
````

## Update the album list template

Finally, now that our middleware is passing albums to the template, we need to update the template to display them.

The list is presented within a table styled by [Bootstrap](http://getbootstrap.com/). We loop through all albums and echo the id, artist, and title by accessing the getter methods of the `AlbumEntity`.

````php
<?php
use App\Model\Entity\AlbumEntity;

$this->layout('layout::default', ['title' => 'Albums']);
?>

<div class="jumbotron">
    <h1>CD Kokoelma</h1>
</div>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Artist</th>
            <th>Title</th>
        </tr>
    </thead>
    <tbody>
        <?php /** @var AlbumEntity $album */ ?>
        <?php foreach ($albumList as $album) : ?>
        <tr>
            <td><?= $this->e($album->getId()); ?></td>
            <td><?= $this->e($album->getArtist()); ?></td>
            <td><?= $this->e($album->getTitle()); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
````

Now you can browse to [http://localhost:8080/](http://localhost:8080/) to see if the album list is shown as expected.
![screen-album-list-with-data](../images/screen-album-list-with-data.png)