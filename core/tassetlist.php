<?php

	if (!defined("TAssetList_DEFINED"))
		{
			Class TAssetList extends TGenericList
				{
					/** @var TAsset[] $items */
					public $items;
					protected $tablename='company_assets';
					protected $idfield='id';
					protected $titlefield='filename';

					function SetFilterCompany($company_or_id)
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id);
						}

					function SetFilterPublic()
						{
							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' access = "public"';
						}

					function SetFilterAudio()
						{
							$supported_types=Array(
								'audio/mpeg',
								'audio/mp3'
							);

							if (!empty($this->sql))
								$this->sql .= ' AND ';
							$this->sql .= ' type IN ('.implode(',', Cleaner::ArrayQuotedAndDBEscaped($supported_types)).')';
						}

					function ExtractTitlesArray()
						{
							$r=Array();
							for ($i = 0; $i < $this->Count(); $i++)
								{
									$r[]=$this->items[$i]->FileNameWithComment();
								}
							return $r;
						}

					protected function InitItem($index)
						{
							$item = new TAsset($this->itemsinfo[$index]);
							return $item;
						}
				}

			define("TAssetList_DEFINED", 1);
		}
