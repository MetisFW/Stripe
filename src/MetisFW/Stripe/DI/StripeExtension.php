<?php

namespace MetisFW\Stripe\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class StripeExtension extends CompilerExtension {

  public function getConfigSchema(): Schema {
    return Expect::structure([
      'publicApiKey' => Expect::string(),
      'secretApiKey' => Expect::string(),
      'paymentIntent' => Expect::array()->default([])
    ]);
  }

  public function loadConfiguration() {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig();

    $builder->addDefinition($this->prefix('Stripe'))
      ->setFactory('MetisFW\Stripe\StripeContext',  array($config->publicApiKey, $config->secretApiKey))
      ->addSetup('setPaymentIntentDefaults', array($config->paymentIntent));

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
