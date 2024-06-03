<?php

	if (!defined("TWebhookItem_DEFINED"))
		{
			Class TWebhookItem extends TGenericObject
				{
					var $info;

					function __construct($id_or_cachedinfo)
						{
							if (is_array($id_or_cachedinfo))
								$this->info=$id_or_cachedinfo;
							else
								$this->info=TQuery::ForTable('webhooks_queue')->AddWhere('id', intval($id_or_cachedinfo))->Get();
						}

					function GetRawData()
						{
							return $this->info;
						}

					public static function UsingCache($id)
						{
							global $sm;
							if (!is_array($sm['cache']['TWebhookItem'][$id]))
								{
									$object=new TWebhookItem($id);
									if ($object->Exists())
										$sm['cache']['TWebhookItem'][$id]=$object->GetRawData();
								}
							else
								$object=new TWebhookItem($sm['cache']['TWebhookItem'][$id]);
							return $object;
						}

					function ID()
						{
							return intval($this->info['id']);
						}

					function Exists()
						{
							return !empty($this->info['id']);
						}


					function AddedTimestamp()
						{
							return intval($this->info['added_time']);
						}

					function SetAddedTimestamp($val)
						{
							$this->UpdateValues(Array('added_time'=>intval($val)));
						}

					function WebhookURL()
						{
							return $this->info['webhook_url'];
						}

					function SetWebhookURL($val)
						{
							$this->UpdateValues(Array('webhook_url'=>$val));
						}

					function GetPostArray()
						{
							return @unserialize($this->PostRequestSerialized());
						}

					protected function PostRequestSerialized()
						{
							return $this->info['post_request'];
						}

					protected function SetPostRequestSerialized($val)
						{
							$this->UpdateValues(Array('post_request'=>$val));
						}

					function UpdateValues($params)
						{
							unset($params['id']);
							if (empty($params) || !is_array($params))
								return;
							$q=new TQuery('webhooks_queue');
							foreach ($params as $key=>$val)
								{
									$this->info[$key]=$val;
									$q->Add($key, dbescape($this->info[$key]));
								}
							$q->Update('id', intval($this->ID()));
						}

					public static function Create($webhook_url, $post_request_array)
						{
							$sql=new TQuery('webhooks_queue');
							$sql->Add('added_time', time());
							$sql->Add('webhook_url', dbescape($webhook_url));
							$sql->Add('post_request', dbescape(serialize($post_request_array)));
							$object=new TWebhookItem($sql->Insert());
							return $object;
						}

					function Remove()
						{
							TQuery::ForTable('webhooks_queue')->AddWhere('id', intval($this->ID()))->Remove();
						}

				}

			define("TWebhookItem_DEFINED", 1);
		}
