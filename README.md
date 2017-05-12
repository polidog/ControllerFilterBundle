# PolidogControllerFilterBundle

This bundle to allow you to describe filtering with annotations in the Controller class.

## Install
   
install polidog/controller-filter-bundle with composer.

```
$ composer require polidog/controller-filter-bundle "@dev"
```

## Configuration

```
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

```
/**
 * @Route("/card")
 * @Filter(Filter::TYPE_BEFORE, method="checkSession")
 */
class CardController extends Controller
{
    use DonationSessionTrait;

    /**
     * @Route("/")
     * @Method("GET")
     * @Template()
     * @Filter(Filter::TYPE_AFTER, method="changeResult")
     */
    public function indexAction(Request $request)
    {
        return ['hoge' =>'fuga'];
    }

    public function checkSession($event)
    {
        if ($this->hasSession()) {
            throw new \Exception();
        }    
    }

    public function changeResult(GetResponseForControllerResultEvent $event)
    {
        $event->setControllerResult(['hoge' => 'hogehoge']);
    }
}
```


