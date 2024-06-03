function  button_msgbox(url, message)
	{
		if (confirm(message))
			{
				setTimeout(function() { document.location.href = url; }, 30);
			}
	}
