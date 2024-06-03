<?php

	class TTag
		{
			var $info;
			function TTag($id_or_cahcedinfo)
				{
					global $sm;
					if (is_array($id_or_cahcedinfo))
						{
							$this->info=$id_or_cahcedinfo;
						}
					else
						{
							$this->info=TQuery::ForTable('company_tags')->Add('id', intval($id_or_cahcedinfo))->Get();
						}
				}
			function Exists()
				{
					return !empty($this->info['id']);
				}
			function Name()
				{
					return $this->info['tag'];
				}
			function ID()
				{
					return intval($this->info['id']);
				}

			function SetAddedBy($val)
				{
					$this->UpdateValues(Array('addedby' => intval($val)));
				}
				
			function UpdateValues($params)
				{
					global $sm;
					unset($params['id']);
					unset($params['id_company']);
					if (empty($params) || !is_array($params))
						return;
					$q=new TQuery('company_tags');
					foreach ($params as $key=>$val)
						{
							$this->info[$key]=$val;
							$q->Add($key, dbescape($this->info[$key]));
						}
					$q->Update('id', $this->ID());
				}
			function AddToCampaign($campaign)
				{
					$this->SetTaxonomy('tagstocampaign', $this->ID(), Cleaner::IntObjectID($campaign));
				}

			function UnsetCampaignID($campaign)
				{
					$this->UnsetTaxonomy('tagstocampaign', $this->ID(), Cleaner::IntObjectID($campaign));
				}

			function GetCustomerIDsArray()
				{
					return $this->GetTaxonomy('customertotags', $this->ID(), true);
				}
			
			function GetEmployeeIDsArray()
				{
					return $this->GetTaxonomy('employeetotags', $this->ID(), true);
				}
			function SetTaxonomy($object_name, $object_id, $rel_id)
				{
					if (is_array($rel_id))
						{
							for ($i = 0; $i<count($rel_id); $i++)
								{
									$this->GetTaxonomy($object_name, $object_id, $rel_id[$i]);
									return;
								}
						}
					if (in_array($rel_id, $this->GetTaxonomy($object_name, $object_id)))
						return;
					$q=new TQuery('taxonomy');
					$q->Add('object_name', dbescape($object_name));
					$q->Add('object_id', intval($object_id));
					$q->Add('rel_id', intval($rel_id));
					$q->Insert();
				}
			function GetTaxonomy($object_name, $object_id, $use_object_id_as_rel_id=false)
				{
					$q=new TQuery('taxonomy');
					$q->Add('object_name', dbescape($object_name));
					if ($use_object_id_as_rel_id)
						{
							$q->Add('rel_id', dbescape($object_id));
							$q->SelectFields('object_id as taxonomyid');
						}
					else
						{
							$q->Add('object_id', dbescape($object_id));
							$q->SelectFields('rel_id as taxonomyid');
						}
					$q->Select();
					return $q->ColumnValues('taxonomyid');
				}
			function UnsetTaxonomy($object_name, $object_id, $rel_id)
				{
					if (is_array($rel_id))
						{
							for ($i = 0; $i<count($rel_id); $i++)
								{
									sm_unset_taxonomy($object_name, $object_id, $rel_id[$i]);
									return;
								}
						}
					$q=new TQuery('taxonomy');
					$q->Add('object_name', dbescape($object_name));
					$q->Add('object_id', intval($object_id));
					$q->Add('rel_id', intval($rel_id));
					$q->Remove();
				}
			public static function Create($company, $tag_name)
				{
					$q=new TQuery('company_tags');
					$q->Add('id_company', Cleaner::IntObjectID($company));
					$q->Add('tag', dbescape($tag_name));
					$id = $q->Insert();
					$tag = new TTag($id);
					return $tag;
				}
		}

?>