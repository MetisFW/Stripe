<?php

namespace MetisFW\Stripe\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\Utils\Validators;

class StripeExtension extends CompilerExtension {

  /**
   * @var array
   */
  public $defaults = array(
    "paymentIntent" => array()
  );

  public function loadConfiguration() {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig($this->defaults);

    Validators::assertField($config, 'publicApiKey', 'string');
    Validators::assertField($config, 'secretApiKey', 'string');
    Validators::assertField($config, 'paymentIntent', 'array');

    $builder->addDefinition($this->prefix('Stripe'))
      ->setClass('MetisFW\Stripe\StripeContext',  array($config['publicApiKey'], $config['secretApiKey']))
      ->addSetup('setPaymentIntentDefaults', array($config['paymentIntent']));

  }

  /**
   * @param Configurator $configurator
   */
  public static function register(Configurator $configurator) {
    $configurator->onCompile[] = function ($config, Compiler $compiler) {
      $compiler->addExtension('stripe', new StripeExtension());
    };
  }

}
