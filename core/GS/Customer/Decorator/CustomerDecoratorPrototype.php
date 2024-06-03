<?php

namespace GS\Customer\Decorator;

/** @method static self Init($customer) */

use TCustomer;

class CustomerDecoratorPrototype
{
	private $customer;

	public function __construct(TCustomer $customer)
	{
		$this->customer = $customer;
	}

	public function Customer(): TCustomer
	{
		return  $this->customer;
	}

	public static function Init(TCustomer $customer)
	{
		return new static($customer);
	}
}
