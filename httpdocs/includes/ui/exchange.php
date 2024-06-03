<?php
	
	//------------------------------------------------------------------------------
	//|                                                                            |
	//|            Content Management System SiMan CMS                             |
	//|                                                                            |
	//------------------------------------------------------------------------------
	
	//==============================================================================
	//#revision 2016-10-15
	//==============================================================================

	if (!defined("ui_exchange_DEFINED"))
		{
			sm_add_jsfile('ext/smuiexchange.js', true);

			/**
			 * Class TExchangeListener
			 *
			 *    Alows to generata javascript code for listen messages form TExchangeSender.
			 *    When message arrived, it will be scanned for ID/Value pairs,
			 *    Each value will be assigned for DOM-element with ID received.
			 */
			class TExchangeListener
				{
					protected $info;

				/**
				 * TExchangeListener constructor.
				 * @param mixed $listener_id - use NULL for using sm_pageid() as listener ID
				 */
					function __construct($listener_id = NULL)
						{
							if ($listener_id === NULL)
								$listener_id = sm_pageid();
							$this->info['id'] = $listener_id;
							$this->info['fields'] = Array();
						}

				/**
				 * Ad an element to listen.
				 * @param $id - DOM-element identoifier
				 * @return $this
				 */
					function Add($id)
						{
							if (!in_array($id, $this->info['fields']))
								$this->info['fields'][] = $id;
							return $this;
						}

				/**
				 * @return string - javascript code for listener
				 */
					function GetJSCode()
						{
							$js = "sm_ls_init_listener('".$this->info['id']."');";
							for ($i = 0; $i < count($this->info['fields']); $i++)
								{
									$js .= "sm_ls_add_listen_key('".$this->ListenerID()."', '".$this->info['fields'][$i]."');";
								}
							return $js;
						}

				/**
				 * @return string - listener ID (initiated in __construct)
				 */
					function ListenerID()
						{
							return $this->info['id'];
						}
				}

			/**
			 * Class TExchangeSender
			 *
			 * Procide an interface for sending messages to TExchangeListener
			 */
			class TExchangeSender
				{
					protected $info;

				/**
				 * TExchangeSender constructor.
				 * @param $listener_id - destination listener ID
				 */
					function __construct($listener_id)
						{
							$this->info['id'] = $listener_id;
							$this->info['fields'] = Array();
						}

				/**
				 * Send a value to listener.
				 * @param $id - destination DOM id from TExchangeListener::Add
				 * @param $value - DOM value. Keep in mind that it should be properly formatted for javascript code
				 * @return $this
				 */
					function Add($id, $value)
						{
							$this->info['fields'][$id] = $value;
							return $this;
						}

				/**
				 * Add a window.close(); for javascript code
				 * @return $this
				 */
					function SetCloseWindowRequest()
						{
							$this->info['close'] = true;
							return $this;
						}

				/**
				 * @return string - javascript code for sender
				 */
					function GetJSCode()
						{
							$js = '';
							foreach ($this->info['fields'] as $id => $val)
								{
									$js .= "sm_ls_send_to_listener('".$this->info['id']."', '".$id."', '".jsescape($val)."');";
								}
							if ($this->info['close'])
								$js .= 'window.close();';
							return $js;
						}

				/**
				 * @return string - listener ID (initiated in __construct)
				 */
					function ListenerID()
						{
							return $this->info['id'];
						}
				}
			
			define("ui_exchange_DEFINED", 1);
		}
