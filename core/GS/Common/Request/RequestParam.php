<?php

	namespace GS\Common\Request;

	class RequestParam extends GenericParam
		{
			private $current_request;
			private $param_name;
			private $param_exists=false;

			function __construct(&$request_array, $request_param_name)
				{
					$this->current_request=&$request_array;
					$this->param_name=$request_param_name;
					if (!isset($this->current_request[$this->param_name]))
						$this->current_request[$this->param_name]=NULL;
					elseif ($this->current_request[$this->param_name]!==NULL)
						$this->param_exists=true;
					parent::__construct($this->current_request[$this->param_name]);
				}

			function Exists(): bool
				{
					return $this->param_exists;
				}

		}
