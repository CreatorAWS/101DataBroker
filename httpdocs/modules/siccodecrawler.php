<?php

	$timeend=time()+58;

	function validate_csv()
		{
			$q = new TQuery('import_data');
			$q->Add('processes', 0);
			$q->AddWhere('SIC_CODE <> ""');
			$q->OrderBy('id');
			$q->Limit(1);
			$q->Select();

			for ($i = 0; $i < $q->Count(); $i++)
				{
					$q = new TQuery('import_data');
					$q->Add('processes', 1);
					$q->Update('id', $q->items[$i]['id']);

					$sic_code = trim($q->items[$i]['SIC_CODE']);
					if (strlen($sic_code) > 6 || strlen($sic_code) < 2)
						return false;

					print($sic_code."\n");

					$filename = 'files/csv/'.$sic_code;

					$outputFilePath = $filename.'.csv';

					if (strlen($sic_code) == 6)
						{
							$q = new TQuery('import_data');
							$q->AddWhere('SIC_CODE = "'.$sic_code.'"');
							$q->Select();

							$outputFile = fopen($outputFilePath, 'w');
							$header = ['COMPANY', 'ADDRESS', 'CITY', 'ST', 'ZIP', 'COUNTYNM', 'PHONE1', 'WEBSITE', 'FIRST', 'LAST', 'TITLE', 'STANDARDIZED_TITLE', 'TITLE_CODE', 'DIR_PHONE', 'EMAIL', 'ETHNICGROUP', 'LANGUAGECODE', 'EMP_RANGE', 'EMP_CODE', 'SALE_RANGE', 'SALE_CODE', 'SIC_CODE', 'INDUSTRY', 'RDI'];
							fputcsv($outputFile, $header);

							for ($j = 0; $j < $q->Count(); $j++)
								{
									$data = [
										$q->items[$j]['COMPANY'],
										$q->items[$j]['ADDRESS'],
										$q->items[$j]['CITY'],
										$q->items[$j]['ST'],
										$q->items[$j]['ZIP'],
										$q->items[$j]['COUNTYNM'],
										$q->items[$j]['PHONE1'],
										$q->items[$j]['WEBSITE'],
										$q->items[$j]['FIRST'],
										$q->items[$j]['LAST'],
										$q->items[$j]['TITLE'],
										$q->items[$j]['STANDARDIZED_TITLE'],
										$q->items[$j]['TITLE_CODE'],
										$q->items[$j]['DIR_PHONE'],
										$q->items[$j]['EMAIL'],
										$q->items[$j]['ETHNICGROUP'],
										$q->items[$j]['LANGUAGECODE'],
										$q->items[$j]['EMP_RANGE'],
										$q->items[$j]['EMP_CODE'],
										$q->items[$j]['SALE_RANGE'],
										$q->items[$j]['SALE_CODE'],
										$q->items[$j]['SIC_CODE'],
										$q->items[$j]['INDUSTRY'],
										$q->items[$j]['RDI']
									];

									fputcsv($outputFile, $data);
								}

							fclose($outputFile);

							$zip = new ZipArchive();
							$zip_filename = $filename.'.zip';
							if ($zip->open($zip_filename, ZipArchive::CREATE)!==TRUE)
								return false;

							$zip->addFile($outputFilePath,'/'.$sic_code.'.csv');
							$zip->close();

							unlink($outputFilePath);
						}
				}
		}
	function grab_data_sic_codes()
		{
			$q = new TQuery('sic_codes');
			$q->Add('processed', 0);
			$q->Add('disabled', 0);
			$q->OrderBy('id');
			$q->Limit(1);
			$q->Select();

			for ($i = 0; $i < $q->Count(); $i++)
				{
					$sic_code_id = $q->items[$i]['id'];

					$q_u = new TQuery('sic_codes');
					$q_u->Add('processed', 1);
					$q_u->Update('id', $sic_code_id);

					$sic_code = trim($q->items[$i]['sic']);
					if (strlen($sic_code) > 6 || strlen($sic_code) < 2)
						return false;

					print($sic_code."\n");

					$filename = 'files/csv/'.$sic_code;

					$outputFilePath = $filename.'.csv';

					$q = new TQuery('import_data');
					if (strlen($sic_code) == 6)
						$q->AddWhere('SIC_CODE = "'.$sic_code.'"');
					else
						$q->AddWhere('SIC_CODE LIKE "'.$sic_code.'%"');
					$q->Select();

					$q_u = new TQuery('sic_codes');
					$q_u->Add('processed', 1);
					$q_u->Add('total_count', $q->Count());
					$q_u->Update('id', $sic_code_id);

					$outputFile = fopen($outputFilePath, 'w');
					$header = ['COMPANY', 'ADDRESS', 'CITY', 'ST', 'ZIP', 'COUNTYNM', 'PHONE1', 'WEBSITE', 'FIRST', 'LAST', 'TITLE', 'STANDARDIZED_TITLE', 'TITLE_CODE', 'DIR_PHONE', 'EMAIL', 'ETHNICGROUP', 'LANGUAGECODE', 'EMP_RANGE', 'EMP_CODE', 'SALE_RANGE', 'SALE_CODE', 'SIC_CODE', 'INDUSTRY', 'RDI'];
					fputcsv($outputFile, $header);

					for ($j = 0; $j < $q->Count(); $j++)
						{
							$data = [
								$q->items[$j]['COMPANY'],
								$q->items[$j]['ADDRESS'],
								$q->items[$j]['CITY'],
								$q->items[$j]['ST'],
								$q->items[$j]['ZIP'],
								$q->items[$j]['COUNTYNM'],
								$q->items[$j]['PHONE1'],
								$q->items[$j]['WEBSITE'],
								$q->items[$j]['FIRST'],
								$q->items[$j]['LAST'],
								$q->items[$j]['TITLE'],
								$q->items[$j]['STANDARDIZED_TITLE'],
								$q->items[$j]['TITLE_CODE'],
								$q->items[$j]['DIR_PHONE'],
								$q->items[$j]['EMAIL'],
								$q->items[$j]['ETHNICGROUP'],
								$q->items[$j]['LANGUAGECODE'],
								$q->items[$j]['EMP_RANGE'],
								$q->items[$j]['EMP_CODE'],
								$q->items[$j]['SALE_RANGE'],
								$q->items[$j]['SALE_CODE'],
								$q->items[$j]['SIC_CODE'],
								$q->items[$j]['INDUSTRY'],
								$q->items[$j]['RDI']
							];

							fputcsv($outputFile, $data);
						}

					fclose($outputFile);

					$zip = new ZipArchive();
					$zip_filename = $filename.'.zip';
					if ($zip->open($zip_filename, ZipArchive::CREATE)!==TRUE)
						return false;

					$zip->addFile($outputFilePath,'/'.$sic_code.'.csv');
					$zip->close();

					unlink($outputFilePath);
				}
		}


	while (time()<=$timeend)
		if (!grab_data_sic_codes())
			break;
