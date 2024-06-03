<?php

	function grab_content($url)
		{
			$c = curl_init($url);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($c, CURLOPT_BINARYTRANSFER, true);
			curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($c, CURLOPT_HTTPHEADER, array('User-Agent: Opera 11'));
			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 0);
			curl_setopt($c, CURLOPT_TIMEOUT, 2);

			$html = curl_exec($c);
			if (curl_error($c))
				return 'error';
			curl_close($c);

			return $html;
		}

	function getCategories($html) {
		$categories = array();

		$dom = new DOMDocument();
		$dom->loadHTML($html);

		// Find all the panel groups
		$panelGroups = $dom->getElementsByTagName('div');

		foreach ($panelGroups as $panelGroup) {
			// Check if the panel group has the "panel-group" and "mb-4" classes
			$classList = $panelGroup->getAttribute('class');
			if (strpos($classList, 'panel-group') !== false && strpos($classList, 'mb-4') !== false) {
				// Find all the panel headings within the panel group
				$panelHeadings = $panelGroup->getElementsByTagName('div');
				foreach ($panelHeadings as $panelHeading) {
					if ($panelHeading->getAttribute('role') == 'tab') {
						// Find the anchor tag within the panel heading
						$anchor = $panelHeading->getElementsByTagName('a')->item(0);
						// Extract the category name and create an empty subcategory array
						$categoryName = trim($anchor->nodeValue);
						$subcategories = array();

						// Find the panel body for the current panel heading
						$panelBodyId = $anchor->getAttribute('href');
						$panelBody = $dom->getElementById(substr($panelBodyId, 1));
						if (!empty($panelBody))
							{
								// Find all the subcategory links within the panel body
								$subcategoryLinks = $panelBody->getElementsByTagName('a');
								foreach ($subcategoryLinks as $subcategoryLink) {
									if ($subcategoryLink->nodeName == 'a') {
										// Extract the subcategory name and add it to the subcategory array
										$subcategoryName = trim($subcategoryLink->nodeValue);
										$subcategoryURL = 'https://trends.builtwith.com'.trim($subcategoryLink->getAttribute('href'));
										if (strpos( $subcategoryName, 'All ')===false)
											$subcategories[] = [
												'title' => $subcategoryName,
												'url' => $subcategoryURL,
											];
									}
								}
							}

						// Add the category and subcategory array to the categories array
						$categories[$categoryName] = $subcategories;
					}
				}
			}
		}
		return $categories;
	}

	function getTechnologies($html) {
		$categories = array();

		$dom = new DOMDocument();
		$dom->loadHTML($html);

		// Find the table within the card body
		$table = $dom->getElementsByTagName('table')->item(0);
		if ($table !== null) {
			// Find all the rows within the table body
			$rows = $table->getElementsByTagName('tr');
			foreach ($rows as $row) {
				// Find the column containing the technology title
				$technologyColumn = $row->getElementsByTagName('td')->item(0);
				if ($technologyColumn !== null) {
					// Find the link within the technology column
					$link = $technologyColumn->getElementsByTagName('a')->item(0);
					if ($link !== null) {
						// Extract the title text and add it to the titles array
						$title = trim($link->nodeValue);
						$titles[] = $title;
					}
				}
			}
		}

		return $titles;
	}


	$categories = new TBuiltWithTechGroupsList();
	$categories->SetFilterMainCategory();
	$categories->Load();

	for ($i = 0; $i < $categories->Count(); $i++)
		{
			/** @var TBuiltWithTechGroup $category */
			$category = $categories->Item($i);

			$url = $category->Url();

			$content = grab_content($url);

			if ($content != 'error' && !empty($content))
				{
					$titles = getTechnologies($content);

					foreach ($titles as $title)
						{
							print($title.'</br>');
							if (!empty($title))
								TBuiltWithTech::Create($title, $category->ID());
						}
				}
		}