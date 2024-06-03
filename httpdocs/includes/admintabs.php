<?php

	if (!defined("admintabs_DEFINED"))
		{
			sm_use('ui.interface');
			sm_add_cssfile('common_admintabs.css');

			class TTabs extends TInterface
				{
					var $activeindex;

					function TTabs($activeindex = 0)
						{
							$this->TInterface('', 0);
							$this->activeindex = $activeindex;
						}

					function SetActiveIndex($activeindex)
						{
							$this->activeindex = $activeindex;
						}

					function SetActiveIndexCurrent()
						{
							$this->activeindex = count($this->blocks)-1;
							if ($this->activeindex==0)
								$this->activeindex=0;
						}

					function Output()
						{
							$this->blocks[$this->activeindex]['active'] = true;
							return $this->blocks;
						}
				}

			define("admintabs_DEFINED", 1);
		}

?>