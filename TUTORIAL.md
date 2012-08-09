# Moving from Doctrine Quickstart to Zend Framework 2

This tutorial is meant to help you migrate from an existing Doctrine 2 project to a 
working ZF2 Doctrine project. This basically catalogues my adventures in trying to
move from a working version of the Doctrine quickstart which can be found here:

http://docs.doctrine-project.org/en/latest/tutorials/getting-started.html

to a working version of the same tutorial in a Zend Framework 2 skeleton application.

Thanks to Marco for his patience, this is aimed to answer the many questions I 
pestered him with in IRC to avoid others having to ask them!

I followed the "Getting Started" tutorial and used the annotations for my mapping
information, you may need to tweak this if you used xml or yaml.

Prerequisits
------------
* Completed version of the Doctrine "Getting Started" tutorial (Bug Tracker)
* Empty working copy of the Zend Framework 2 Skeleton Application

https://github.com/zendframework/ZendSkeletonApplication

Installing
----------
Follow the installation instructions for DoctrineORM Module at

https://github.com/doctrine/DoctrineORMModule

When creating the drivers, the simpliest way is to create a doctrine.global.cfg that contain your annotation drivers, and a doctrine.local.cfg that contains your database drivers. The path to your entities can then be set to `__DIR__ . '/../../module/Application/src/Application/Entities'`; this is probably not the best way of locating the files, but it works for now!

We now need to move the files from the Doctrine Quickstart project into our ZF2 application. For the sake of this tutorial I will be using the default `Application` module that is part of the skeleton application, but obviously in the real world you may keep these files elsewhere. 

Firstly, in the `Application/src/Application` directory, create directories called `Entities` and `Repositories`, this is where we are going to keep our doctrine code. Next, copy all the files from your Doctrine project's `repositories` directory into the newly created `Repositories` directory, and from the Doctrine project's `entities` directory to the newly created `Entities` directory. We now need to modify these classes so they work with ZF2 namespacing and autoloading.

Entities
--------
The entity classes need to be namespaced as `Application\Entities`, and we also need to use `Doctrine\ORM\Mapping as ORM`. We then need to preface all the Doctrine annotations with `ORM\` so that the annotation parser knows they are Doctrine annotations, and not ZF2 annotations. We also need to change any annotations to use the FQN of classes that are included. As an example, once you've modified Application/Entities/Bug.php it should look something like:

```php
    <?php
    namespace Application\Entities;
    
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\Mapping as ORM;
    
    /**
     * @ORM\Entity(repositoryClass="Application\Repositories\BugRepository")
     * @ORM\Table(name="bugs")
     **/
    class Bug
    {
        /**
         * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
         **/
        protected $id;
        /**
         * @ORM\Column(type="string")
         **/
        protected $description;
        /**
         * @ORM\Column(type="datetime")
         **/
        protected $created;
        /**
         * @ORM\Column(type="string")
         **/
        protected $status;
        /**
         * @ORM\ManyToOne(targetEntity="Application\Entities\User", inversedBy="assignedBugs")
         **/
        protected $engineer;
        /**
         * @ORM\ManyToOne(targetEntity="Application\Entities\User", inversedBy="reportedBugs")
         **/
        protected $reporter;
        /**
         * @ORM\ManyToMany(targetEntity="Application\Entities\Product")
         **/
        protected $products;
    
    
        public function __construct()
        {
            $this->products = new ArrayCollection();
        }
    
        public function setEngineer($engineer)
        {
            $engineer->assignedToBug($this);
            $this->engineer = $engineer;
        }
    
        public function getEngineer()
        {
            return $this->engineer;
        }
    
        public function setReporter($reporter)
        {
            $reporter->addReportedBug($this);
            $this->reporter = $reporter;
        }
    
        public function getReporter()
        {
            return $this->reporter;
        }
    
        public function assignToProduct($product)
        {
            $this->products[] = $product;
        }
    
        public function getProducts()
        {
            return $this->products;
        }
    
        public function setDescription($description)
        {
            $this->description = $description;
        }
    
        public function setCreated($created)
        {
            $this->created = $created;
        }
    
        public function setStatus($status)
        {
            $this->status = $status;
        }
    
        public function getId()
        {
            return $this->id;
        }
    
        public function getCreated()
        {
            return $this->created;
        }
    
        public function getDescription()
        {
            return $this->description;
        }
    
        public function getStatus()
        {
            return $this->status;
        }
    
        public function close()
        {
            $this->setStatus('CLOSE');
        }
    
    }
```    
    
Repositories
------------
Similar changes need to be made to the `BugRespository` code to get the DQL queries to work. Firstly, it needs to be namespaced in the same way as the entities, and any references to the entities needs to be changed to the FQN of that entity. So your modified `Application\Repositories\BugRepository.php` code should look like:

```php
    <?php
    namespace Application\Repositories;
    
    use Doctrine\ORM\EntityRepository;
    use Application\Entities\Bug;
    use Application\Entities\Product;
    use Application\Entities\User;
    
    class BugRepository extends EntityRepository
    {
        public function getRecentBugs($number = 30)
        {
            $dql = "SELECT b, e, r FROM Application\Entities\Bug b JOIN b.engineer e JOIN b.reporter r ORDER BY b.created DESC";
    
            $query = $this->getEntityManager()->createQuery($dql);
            $query->setMaxResults($number);
            return $query->getResult();
        }
    
        public function getRecentBugsArray($number = 30)
        {
            $dql = "SELECT b, e, r, p FROM Application\Entities\Bug b JOIN b.engineer e ".
                "JOIN b.reporter r JOIN b.products p ORDER BY b.created DESC";
            $query = $this->getEntityManager()->createQuery($dql);
            $query->setMaxResults($number);
            return $query->getArrayResult();
        }
    
        public function getUsersBugs($userId, $number = 15)
        {
            $dql = "SELECT b, e, r FROM Application\Entities\Bug b JOIN b.engineer e JOIN b.reporter r ".
                "WHERE b.status = 'OPEN' AND e.id = ?1 OR r.id = ?1 ORDER BY b.created DESC";
    
            return $this->getEntityManager()->createQuery($dql)
                ->setParameter(1, $userId)
                ->setMaxResults($number)
                ->getResult();
        }
     
        public function getOpenBugsByProduct()
        {
            $dql = "SELECT p.id, p.name, count(b.id) AS openBugs FROM Application\Entities\Bug b ".
                "JOIN b.products p WHERE b.status = 'OPEN' GROUP BY p.id";
            return $this->getEntityManager()->createQuery($dql)->getScalarResult();
        }
    }
```    

Testing
-------
The easiest way to check everything is installed correctly is to move to the `vendor/doctrine/doctrine-module/bin` directory, and run `./doctrine-module orm:validate-schema`, this should tell you any errors in your configuration. If you run this now, you should see no errors:

    [Mapping]  OK - The mapping files are correct.
    [Database] OK - The database schema is in sync with the mapping files.

Service Manager
---------------
Once you are at this stage, you should now have an instance of the Doctrine entity manager available in the global service manager under the key `Doctrine\ORM\EntityManager`. It is easy to test this in the index controller by simply running the code:

```php
    public function indexAction()
    {
        var_dump($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'));
    }
```    

You should see dumped a configured instance of `Doctrine\ORM\EntityManager`. Lets use the service manager to inject the entity manager into our IndexController, and use the entity manager to retreive and query the BugRepository. Firstly, we need to modify IndexController.php to accept an instance of the entity manager in it's contructor:

```php
    <?php
    namespace Application\Controller;
    
    use Zend\Mvc\Controller\AbstractActionController;
    use Doctrine\ORM\EntityManager;
    use Zend\View\Model\ViewModel;
    
    class IndexController extends AbstractActionController
    {
        /**
         * @var EntityManager
         */
        protected $em;
    
        public function __construct(EntityManager $em)
        {
            $this->em = $em;
        }
```        

Next, we need to modify the controller entry in `module.config.php` to change it from an invokable to a factory:

```php
    'controllers' => array(
        'factories' => array(
            'Application\Controller\Index' => function(Zend\Mvc\Controller\ControllerManager $sm)
            {
                $em = $sm->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $controller = new Application\Controller\IndexController($em);
                return $controller;
            }
        ),
    ),
```    

All we are doing here is telling the service manager that when it is requested the key `Application\Controller\Index`, retrieve the EM from the service manager, and inject it into the contructor of our index controller.

Finally, we can use the EM in our index controller to retrieve and query the bug repository:

```php
    public function indexAction()
    {
        $bug = $this->em->getRepository('Application\Entities\Bug');
        var_dump($bug->getRecentBugsArray());
        return new ViewModel();
    }
```

This should dump out the recent bugs array from the database (that you created using the command line tools in the Doctrine quickstart).

Fin
---
That's it, feel free to give me a nudge in #zftalk.2 on freenode (I'm `Spabby`), or email me at its.spabby@gmail.com if you need any more help. A massive thank you to Marco Pivetta (`ocramius`), without several hours of bothering him I would never have got this running in the first place.
