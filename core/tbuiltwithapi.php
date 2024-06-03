<?php

	class TBuiltWithAPI
		{
			private $error = NULL;
			private $key = NULL;
			private $nextoffset = NULL;
			private $response = [];

			public function __construct()
				{
					$this->key = urlencode(BuiltWithAPIKey());
				}

			function SetError($error)
				{
					$this->error = $error;
				}

			public function Error()
				{
					return $this->error;
				}

			public function HasErrors()
				{
					return !empty($this->error);
				}

			function GerResults()
				{
					return $this->response;
				}

			function GerNextOffset()
				{
					return $this->nextoffset;
				}

			function SetNextOffset($offset)
				{
					$this->nextoffset = $offset;
				}

			function ParseResponse($results)
				{
					$result = [];

					foreach ($results as $item)
						{
							if ( empty($item['META']) || empty($item['META']['CompanyName']) || empty($item['META']['Emails']) )
								continue;

							$result[] = [
								'domain' => $item['D'],
								'company' => $item['META']['CompanyName'],
								'social' => $item['META']['Social'],
								'phones' => $item['META']['Telephones'],
								'emails' => $item['META']['Emails'],
								'city' => $item['META']['City'],
								'state' => $item['META']['State'],
								'zip' => $item['META']['Postcode'],
								'country' => $item['META']['Country']
							];
						}

					$this->response = $result;
				}

			function GetTechCompaniesList($tech)
				{
					$url = 'https://api.builtwith.com/lists11/api.json?KEY='.$this->key.'&TECH='.urlencode($tech).'&META=yes';
					$response = file_get_contents($url);
					$data = json_decode($response, true);

					if (!empty($data['error']))
						self::SetError($data['error']);
					else
						{
							$this->SetNextOffset($data['NextOffset']);
							$this->ParseResponse($data['Results']);
						}
				}

		}