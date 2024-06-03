<?php

	$timeend = time() + 59;

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

	function get_email($content)
		{
			$email = '';

			$emails = $content;
			for ($i = 0; $i<count($emails); $i++)
				{
					if (is_email($emails[$i]))
						{
							$email = strtolower($emails[$i]);
							break;
						}
				}
			return $email;
		}

	function check_email($url)
		{
			$content = grab_content($url);

			if ($content != 'error' && !empty($content))
				{
					$dom = new DOMDocument();
					$dom->loadHTML($content);
					$script = $dom->getElementsByTagName('script');
					$remove = [];
					foreach($script as $item)
						{
							$remove[] = $item;
						}
					foreach ($remove as $item)
						{
							$item->parentNode->removeChild($item);
						}
					$content = $dom->saveHTML();

					$dom = new DOMDocument();
					$dom->loadHTML($content);
					$script = $dom->getElementsByTagName('style');
					$remove = [];
					foreach($script as $item)
						{
							$remove[] = $item;
						}
					foreach ($remove as $item)
						{
							$item->parentNode->removeChild($item);
						}
					$content = $dom->saveHTML();

					$dom = new DOMDocument();
					$dom->loadHTML($content);
					$script = $dom->getElementsByTagName('picture');
					$remove = [];
					foreach($script as $item)
						{
							$remove[] = $item;
						}
					foreach ($remove as $item)
						{
							$item->parentNode->removeChild($item);
						}
					$content = $dom->saveHTML();

					$content = preg_replace('/<(\s*)img[^<>]*>/i', '', $content);
					$instagram_url = '';
					$twitter_url = '';
					$fb_page = '';
					$get_email = '';

					$content = htmlentities(str_replace('www.', '', $content));

					$fb_url = strpos($content, 'facebook.com/');
					if ($fb_url)
						{
							$start = strpos($content, 'facebook.com/') + 13;
							$string = substr($content, $start);

							$end = strpos($string, '&quot;');
							$page_id = substr($content, $start, $end);
							if (!empty($page_id))
								$fb_page = 'https://facebook.com/'.$page_id;
						}

					$twitter_url = strpos($content, 'twitter.com/');
					if ($twitter_url)
						{
							$start = strpos($content, 'twitter.com/') + 12;
							$string = substr($content, $start);
							$end = strpos($string, '&quot;');
							$page_id = substr($content, $start, $end);
							if (!empty($page_id))
								$twitter_url = 'https://twitter.com/'.$page_id;
						}

					$linkedin_url = strpos($content, 'linkedin.com/');
					if ($linkedin_url)
						{
							$start = strpos($content, 'linkedin.com/') + 13;
							$string = substr($content, $start);

							$end = strpos($string, '&quot;');
							$page_id = substr($content, $start, $end);

							if (!empty($page_id))
								$linkedin_url = 'https://linkedin.com/'.$page_id;
						}

					$instagram_url = strpos($content, 'instagram.com/');
					if ($instagram_url)
						{
							$start = strpos($content, 'instagram.com/') + 14;
							$string = substr($content, $start);

							$end = strpos($string, '&quot;');
							$page_id = substr($content, $start, $end);
							if (!empty($page_id))
								$instagram_url = 'https://instagram.com/'.$page_id;
						}

					$pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
					$res = preg_match_all($pattern, $content, $matches);

					if ($res)
						{
							$get_email = get_email($matches[0]);
						}
					else
						{
							$url = preg_replace('/\?.*/', '', $url);

							$content = grab_content($url.'/contact');

							$dom = new DOMDocument();
							$dom->loadHTML($content);
							$script = $dom->getElementsByTagName('script');
							$remove = [];
							foreach($script as $item)
								{
									$remove[] = $item;
								}
							foreach ($remove as $item)
								{
									$item->parentNode->removeChild($item);
								}
							$content = $dom->saveHTML();

							$dom = new DOMDocument();
							$dom->loadHTML($content);
							$script = $dom->getElementsByTagName('style');
							$remove = [];
							foreach($script as $item)
								{
									$remove[] = $item;
								}
							foreach ($remove as $item)
								{
									$item->parentNode->removeChild($item);
								}
							$content = $dom->saveHTML();

							$dom = new DOMDocument();
							$dom->loadHTML($content);
							$script = $dom->getElementsByTagName('picture');
							$remove = [];
							foreach($script as $item)
								{
									$remove[] = $item;
								}
							foreach ($remove as $item)
								{
									$item->parentNode->removeChild($item);
								}
							$content = $dom->saveHTML();

							$content = preg_replace('/<(\s*)img[^<>]*>/i', '', $content);

							if ($content !== 'error')
								{
									$res = preg_match_all($pattern, $content, $matches);
									if ($res)
										{
											$get_email = get_email($matches[0]);
										}
								}
						}
				}
			return [
				'facebook' => $fb_page,
				'twitter' => $twitter_url,
				'instagram' => $instagram_url,
				'linkedin' => $linkedin_url,
				'email' => $get_email,
			];
		}

	function import_lead_emails()
		{
			$items = new TGoogleLeadsList();
			$items->SetFilterCheckEmail();
			$items->Limit(1);
			$items->OrderByID();
			$items->Load();

			if ($items->Count()==0)
				return false;

			/** @var $item TGoogleLeads */
			$item = $items->Item(0);
			$result = check_email($item->Website());
			$item->SetEmailChecked();
			$item->SetEmail($result['email']);
			$item->SetFacebookUrl($result['facebook']);
			$item->SetTwitterUrl($result['twitter']);
			$item->SetInstagramUrl($result['instagram']);
			$item->SetLinkedin($result['linkedin']);

			if ($item->HasCustomerID())
				{
					$customer = new TCustomer($item->CustomerID());
					if ($customer->Exists())
						{
							$customer->SetEmail($result['email']);
							$customer->SetFacebookUrl($result['facebook']);
							$customer->SetTwitterUrl($result['twitter']);
							$customer->SetInstagramUrl($result['instagram']);
							$customer->SetLinkedin($result['linkedin']);
						}
				}

			unset($item);

			return true;
		}

	while (time()<=$timeend)
		{
			if (!import_lead_emails())
				sleep(5);
		}