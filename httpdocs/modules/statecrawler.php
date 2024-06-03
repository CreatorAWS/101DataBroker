<?php

	$timeend=time()+58;

	function grab_data_states()
		{
			$q = new TQuery('states');
			$q->Add('processed', 0);
			$q->OrderBy('id');
			$q->Limit(1);
			$q->Select();

			for ($i = 0; $i < $q->Count(); $i++)
				{
					$state_id = $q->items[$i]['id'];

					$q_u = new TQuery('states');
					$q_u->Add('processed', 1);
					$q_u->Update('id', $state_id);

					$state_abbr = $q->items[$i]['state_abbr'];

					$filename = 'files/csv_states/'.$state_abbr;

					$outputFilePath = $filename.'.csv';

					$q = new TQuery('import_data');
					$q->AddWhere('ST = "'.$state_abbr.'"');
					$q->Select();

					$q_u = new TQuery('states');
					$q_u->Add('processed', 1);
					$q_u->Add('total_count', $q->Count());
					$q_u->Update('id', $state_id);

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

					$zip->addFile($outputFilePath,'/'.$state_abbr.'.csv');
					$zip->close();

					unlink($outputFilePath);
				}
		}


	while (time()<=$timeend)
		if (!grab_data_states())
			break;
