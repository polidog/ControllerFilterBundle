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
 * @Filter(Filter::TYPE_BEFORE, method="test")
 */
class CardController extends Controller
{
    use DonationSessionTrait;

    /**
     * @Route("/")
     * @Method("GET")
     * @Template()
     * @Filter(Filter::TYPE_AFTER, method="test2")
     */
    public function indexAction(Request $request)
    {
        if ($this->hasSession()) {

        }
        return ['hoge' =>'fuga'];
    }

    public function test($event)
    {
        if ($this->hasSession()) {
            throw new \Exception();
        }    
    }

    public function test2(GetResponseForControllerResultEvent $event)
    {
        $event->setControllerResult(['hoge' => 'hogehoge']);
    }
}
```


