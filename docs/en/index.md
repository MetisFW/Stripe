# MetisFW/Stripe

## Setup

1) Register extension
```
extensions:
  stripe: MetisFW\Stripe\DI\StripeExtension
```

2) Set up extension parameters

```neon
stripe:
  secretApiKey: "sk_live_*"
  publicApiKey: "pk_live_*"
  paymentIntent:
    currency: "USD"
```

paymentIntent is default config to [Stripe PaymentIntent](https://stripe.com/docs/api/payment_intents/object)


##Usage

##### Sample usage of `PaymentControl`

###### In Presenter

```php
use \MetisFW\Stripe\Payment\SimplePaymentOperation;
use \MetisFW\Stripe\UI\PaymentControl;
use Stripe\Api\Payment;
use Nette\Application\UI\Presenter;

class MyPresenter extends Presenter {

  public function createComponentStripePayment(SimplePaymentOperationFactory $factory) {
    $operation = $factory->create('Coffee', 5);
    $control = new PaymentControl($operation);
  
    //set different template if u want to use own
    $control->setTemplateFilePath(__DIR__ . './myStripePayment.latte');

    //called after successfully completed payment proccess
    $control->onSuccess[] = function(PaymentControl $control, Payment $paid) {
      //something
    };
  
    //called when payment process fails
    $control->onError[] = function(PaymentControl $control) {
      //something
    };
  
    return $control;
  }
}
```

###### In latte

```latte
#just
{control stripePayment}

#or

#cannot use attributes directly in control
{var attributes = array('class' => 'stripe-payment-button')} 
{control stripePayment $attributes, 'Pay me now!'}
```

##### Sample usage of `SimplePaymentOperation`

```php
  public function createComponentStripePayment(SimplePaymentOperationFactory $factory) {
    $operation = $factory->create('Coffee', 10);
    $control = new PaymentControl($operation);
    return $control;
  }
```

##### Sample usage of `PlainPaymentOperation`

```php
use Stripe\Api\Transaction;

  public function createComponentStripePayment(PlainPaymentOperationFactory $factory) {
    // see https://stripe.com/docs/api/payment_intents/object
    $params = array(
      'amount' => 5,
      'description' => "Coffeee"
    );
  
    $operation = $factory->create($params);
    $control = new PaymentControl($operation);
    return $control;
  }
```

##### Sample usage of own descendant `\MetisFW\Stripe\Payment\BasePaymentOperation`

```php
<?php

namespace MetisApp\Components\Payment;

use MetisFW\Stripe\Payment\BasePaymentOperation;
use MetisFW\Stripe\StripeContext;

class OrderStripeOperation extends BasePaymentOperation {

  /** @var Order */
  private $order;

  /**
   * @param StripeContext $context
   * @param mixed $order some data - object/array/...
   */
  public function __construct(StripeContext $context, Order $order) {
    parent::__construct($context);
    $this->order = $order;
  }

  /**
   * @return array
   */
  protected function getPaymentIntentParams() {
    return array(
      'amount' => $order->totalAmount,
      'description' => $order->orderNumber
    );
  }

}

```

###### Events in Operation
```php
  public function createComponentStripePaymentButton(FactorType $factory) {
    $operation = $factory->create();
    $operation->onSuccess[] = function(PaymentOperation $operation, PaymentIntent $paid) {
      //something
    }
    $operation->onError[] = function(PaymentOperation $operation) {
      //something
    }
    
    ...
  }
```
