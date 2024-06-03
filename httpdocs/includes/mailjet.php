<?php
namespace ParseRoute;

class Route
{
    private $api_key_public;
    private $api_key_private;
    private $error;
    private $api_sender_path = "https://api.mailjet.com/v3/REST/sender";
    private $api_dns_path = "https://api.mailjet.com/v3/REST/dns";
    private $add_route_path = "https://api.mailjet.com/v3/REST/parseroute";

    // constructor
    public function __construct($key_public, $key_private)
    {
        $this->api_key_public = $key_public;
        $this->api_key_private = $key_private;
    }

	function ErrorMessage()
		{
			return $this->error;
		}

    function HasError()
		{
			return !empty($this->error);
		}

    /*** Create a new sender email address or domain
    Method - POST
    Params -Email, Customer Name
     ***/
    public function create($name, $email)
    {
        $params = array(
            'EmailType' => 'bulk',
            'IsDefaultSender' => false,
            'Name' => $name,
            'Email' => $email,
        );
        $response = json_decode($this->curl_request('set_sender', $params), true);

        if (array_key_exists("ErrorInfo", $response))
			{
				$error_message = $response['ErrorMessage'];
				return $error_message;
			}

        return $response;

        // $count = $response['Count'];
        // $total = $response['Total'];

        // $name = $response['Data'][0]['Name'];
        // $CreatedAt = $response['Data'][0]['CreatedAt'];
        // $DNSID = $response['Data'][0]['DNSID'];
        // $id = $response['Data'][0]['ID'];
        // $status = $response['Data'][0]['Status'];

        // return $DNSID;
    }

    /*** Get information on a list of sender email addresses and/or domains.
    The e-mail address or domain has to be registered and validated before being used to send e-mails
    Validation is done via validate method
    Method - POST
    No Params
     ***/
    public function senders()
    {
        $response = json_decode($this->curl_request('get_senders'), true);

        if (array_key_exists("ErrorInfo", $response)) {
            $error_message = $response['ErrorMessage'];
            return $error_message;
        }
        return $response;
    }
    
    /*** Get information on a specific sender email or sender Id.
    Method - GET
    Params -SenderID
     ***/
    public function getSender($sender_id)
    {
        $params = array();
        $response = json_decode($this->curl_request('get_sender_details', $params, $sender_id), true);

        if (array_key_exists("ErrorInfo", $response)) {
            $error_message = $response['ErrorMessage'];
            return $error_message;
        }
        return $response;
    }
    /*** Validate a registered sender email address or domain.
    A sender domain (*@domain.com) is validated by checking the caller's rights, the existence of a metasender for that domain or by searching for the ownership token on the domain root or in the DNS.
    Method - POST
    Params -SenderID
     ***/
    public function validateSender($sender_id)
		{
			$params = array();
			$response = json_decode($this->curl_request('validate_sender', $params, $sender_id), true);

			if (isset($response['Errors']) && count($response['Errors']) > 0)
				$this->error = $response['Errors']['DNSValidationError'];
			elseif (array_key_exists("ErrorInfo", $response))
				$this->error = $response['ErrorMessage'];
			else
				return $response;
		}

    /*** Delete an existing sender email address or domain.
    Method - POST
    Params -SenderID
     ***/
    public function deleteSender($sender_id)
    {
        $params = array();
        $response = json_decode($this->curl_request('delete_sender', $params, $sender_id), true);

        if (array_key_exists("ErrorInfo", $response)) {
            $error_message = $response['ErrorMessage'];
            return $error_message;
        }

        return $response;
    }

    /*** Create a new parseroute instance. Provide the webhook URL you want to use
    Method - POST
    Params -Email, Webhook Url
     ***/
    public function addRoute($email, $url)
    {
        $params = array(
            'Url' => $url,
            'Email' => $email,
        );
        $response = json_decode($this->curl_request('add_route', $params), true);

        if (array_key_exists("ErrorInfo", $response)) {
            $error_message = $response['ErrorMessage'];
            return $error_message;
        }

        return $response;

        // $count = $response['Count'];
        // $total = $response['Total'];

        // $APIKeyID = $response['Data'][0]['APIKeyID'];
        // $Email = $response['Data'][0]['Email'];
        // $Url = $response['Data'][0]['Url'];
        // $route_id = $response['Data'][0]['ID'];

        // return $add_route_id;
    }
    /*** Retrieve a list of all parseroute instances and their configuration settings.
    Method - POST
    Params -Email, Webhook Url
     ***/
    public function getRoutes()
    {
        $response = json_decode($this->curl_request('get_routes'), true);
        if (array_key_exists("ErrorInfo", $response)) {
            $error_message = $response['ErrorMessage'];
            return $error_message;
        }
        return $response;
    }
    /*** Update the settings of an existing parseroute instance - the webhook URL and/or email address.
    Method - PUT
    Params -Route ID Email and/or Url
     ***/
    public function updateRoute($route_id, $url = null, $email = null)
    {
        $params = array(
            'Url' => $url,
            'Email' => $email,
        );
        $response = json_decode($this->curl_request('update_route', $params, $route_id), true);

        $count = $response['Count'];
        $total = $response['Total'];

        $APIKeyID = $response['Data'][0]['APIKeyID'];
        $Email = $response['Data'][0]['Email'];
        $Url = $response['Data'][0]['Url'];
        $add_route_id = $response['Data'][0]['ID'];
        return $add_route_id;
    }
    /*** Update the settings of an existing parseroute instance - the webhook URL and/or email address.
    Method - DELETE
    Params -route id / email address of the route
     ***/
    public function deleteRoute($route_id)
    {
        $params = array();
        $response = json_decode($this->curl_request('delete_route', $params, $route_id), true);

        return $response;
    }
    /*** Retrieve the SPF and DKIM settings for all sender domains on this API Key.
    The information will help you configure your SPF and DKIM settings on the respective domain, before running a DNS check to validate it and complete the SPF and DKIM authentication.
    Method - GET

     ***/
    public function getDNS()
    {
        $response = json_decode($this->curl_request('get_dns'), true);
        return $response;
        return true;
    }

    /*** Retrieve the SPF and DKIM settings for a sender domain
    Method - GET
    Params -DNS ID
     ***/
    public function dns($dns_id)
    {
        $params = array();
        $response = json_decode($this->curl_request('dns_fetch', $params, $dns_id), true);

        if (array_key_exists("ErrorInfo", $response)) {
            $error_message = $response['ErrorMessage'];
            return $error_message;
        }

        return $response;
        // $count = $response['Count'];
        // $total = $response['Total'];

        // $DKIMRecordName = $response['Data'][0]['DKIMRecordName'];
        // $DKIMRecordValue = $response['Data'][0]['DKIMRecordValue'];
        // $DKIMStatus = $response['Data'][0]['DKIMStatus'];

        // $domain = $response['Data'][0]['Domain'];
        // $SPFRecordValue = $response['Data'][0]['SPFRecordValue'];
        // $SPFStatus = $response['Data'][0]['SPFStatus'];
        // $OwnerShipToken = $response['Data'][0]['OwnerShipToken'];

        // $id = $response['Data'][0]['ID'];

        // return true;
    }
    /*** check DNS  status
    Method - GET
    Params -dns id
     ***/
    public function dns_check($dns_id)
    {
        $params = array();
        $response = json_decode($this->curl_request('dns_check', $params, $dns_id), true);
		return $response;

//		$count = $response['Count'];
//        $total = $response['Total'];
//
//        $DKIMRecordName = $response['Data'][0]['DKIMRecordName'];
//        $DKIMRecordValue = $response['Data'][0]['DKIMRecordValue'];
//        $DKIMStatus = $response['Data'][0]['DKIMStatus'];
//
//        $domain = $response['Data'][0]['Domain'];
//        $SPFRecordValue = $response['Data'][0]['SPFRecordValue'];
//        $SPFStatus = $response['Data'][0]['SPFStatus'];
//        $OwnerShipToken = $response['Data'][0]['OwnerShipToken'];
//
//        $id = $response['Data'][0]['ID'];
//
//        return $DKIMStatus;
    }
    private function curl_request($function, $parameters = array(), $resource_id = '')
    {

        $curl_handle = curl_init();

        switch ($function) {
            case 'set_sender':
                curl_setopt($curl_handle, CURLOPT_URL, $this->api_sender_path);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl_handle, CURLOPT_POST, 1);
                curl_setopt($curl_handle, CURLOPT_POSTFIELDS, json_encode($parameters));
                break;
            case 'get_senders':
                curl_setopt($curl_handle, CURLOPT_URL, $this->api_sender_path);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);

                break;
            case 'get_sender_details':
                curl_setopt($curl_handle, CURLOPT_URL, $this->api_sender_path. '/' . $resource_id);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);

                break;
            case 'validate_sender':
                curl_setopt($curl_handle, CURLOPT_URL, $this->api_sender_path . '/' . $resource_id . '/validate');
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl_handle, CURLOPT_POST, 1);
                curl_setopt($curl_handle, CURLOPT_POSTFIELDS, json_encode($parameters));
                break;
            case 'delete_sender':
                curl_setopt($curl_handle, CURLOPT_URL, $this->api_sender_path . '/' . $resource_id);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'add_route':
                curl_setopt($curl_handle, CURLOPT_URL, $this->add_route_path);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl_handle, CURLOPT_POST, 1);
                curl_setopt($curl_handle, CURLOPT_POSTFIELDS, json_encode($parameters));
                break;
            case 'get_routes':
                curl_setopt($curl_handle, CURLOPT_URL, $this->add_route_path);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                break;
            case 'update_route':
                $params = array();
                foreach ($parameters as $key => $value) {
                    if (!is_null($value)) {
                        $params[$key] = $value;
                    }
                }
                curl_setopt($curl_handle, CURLOPT_URL, $this->add_route_path . '/' . $resource_id);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($curl_handle, CURLOPT_POSTFIELDS, json_encode($params));
                break;
            case 'delete_route':
                curl_setopt($curl_handle, CURLOPT_URL, $this->add_route_path . '/' . $resource_id);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'get_dns':
                curl_setopt($curl_handle, CURLOPT_URL, $this->api_dns_path);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                break;
            case 'dns_fetch':
                curl_setopt($curl_handle, CURLOPT_URL, $this->api_dns_path . '/' . $resource_id);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                break;
            case 'dns_check':
                curl_setopt($curl_handle, CURLOPT_URL, $this->api_dns_path . '/' . $resource_id . '/check');
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl_handle, CURLOPT_POST, 1);
                break;
            default:
                return 'Method not given';
        }

        curl_setopt($curl_handle, CURLOPT_USERPWD, $this->api_key_public . ':' . $this->api_key_private);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
        // $result = curl_exec($curl_handle);
        if (curl_errno($curl_handle)) {
            return 'Error:' . curl_error($curl_handle);
        }
        $output = curl_exec($curl_handle);
        $httpcode = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
        curl_close($curl_handle);

        return $output;
    }

}
