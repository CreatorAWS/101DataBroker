<?php

	namespace GS\Common\Interfaces;

	interface ShreableInterface
		{
			function FbShareAvailable(): bool;
			function TwitterShareAvailable(): bool;
			function LinkedinShareAvailable(): bool;
			function TextShareAvailable(): bool;
			function EmailShareAvailable(): bool;

		}