# PolidogControllerFilterBundle

[![Build Status](https://travis-ci.org/polidog/ControllerFilterBundle.svg?branch=master)](https://travis-ci.org/polidog/ControllerFilterBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/polidog/ControllerFilterBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/polidog/ControllerFilterBundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/polidog/ControllerFilterBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/polidog/ControllerFilterBundle/?branch=master)

This bundle to allow you to describe filtering with annotations in the Controller class.

## Install
   
install polidog/controller-filter-bundle with composer.

```
$ composer require polidog/controller-filter-bundle
```

## Configuration

```php
// AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Polidog\ControllerFilterBundle\PolidogControllerFilterBundle(),
        // ...
    );
}
```

## Using

```php
// controller class

/**
 * @Route("/card")
 * @Filter(Filter::TYPE_BEFORE, method="checkSession", service="app.service.check_service")
 */
class CardController extends Controller
{
    use DonationSessionTrait;

    /**
     * @Route("/")
     * @Method("GET")
     * @Template()
     * @Filter(Filter::TYPE_AFTER, method="changeResult", service="app.service.check_service")
     */
    public function indexAction(Request $request)
    {
        return ['hoge' =>'fuga'];
    }



}

```

```php
// service class
class CheckService {
    /** 
     * @Session
     */
    private $session;
    
    
    public function changeResult()
    {
        if ($this->session->has('hoge') {
            throw new \Exception();
        }
    }
    
    public function changeResult(GetResponseForControllerResultEvent $event)
    {
        $event->setControllerResult(['hoge' => 'hogehoge']);
    }    
}

```

```yaml
// services.yml

services:
    app.service.check_service:
        class: AppBundle\Service\CheckService
        public: true
        arguments: ["@session"]

```

