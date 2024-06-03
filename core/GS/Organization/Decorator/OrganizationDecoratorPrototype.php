<?php

namespace GS\Organization\Decorator;

/** @method static self Init($organization) */

use TOrganization;

class OrganizationDecoratorPrototype
{
	private $organization;

	public function __construct(TOrganization $organization)
	{
		$this->organization = $organization;
	}

	public function Organization(): TOrganization
	{
		return  $this->organization;
	}

	public static function Init(TOrganization $organization)
	{
		return new static($organization);
	}
}
