<?php
/**
 * Created by PhpStorm.
 * User: yvanwatchman
 * Date: 25-04-18
 * Time: 10:37
 */

namespace Cyberfusion;


class Stiply
{

    public $username, $password;
    public $data;

    /**
     * Stiply constructor.
     * @param array $user
     */
    public function __construct(array $user)
    {
        $this->username = $user['username'];
        $this->password = $user['password'];
    }

    /**
     *
     * Check ping to see if API con works
     * @param $data
     * @param $type
     * @return bool|mixed
     */
    public function checkPingRequest($data, $type)
    {
        if (isset($data, $type)) {
            $this->data = json_encode($data);
            $this->executeRequest("/actions/ping", $type);
            return true;
        }
        return false;
    }

    /**
     * Handles the requests to the API
     * @param $endpoint
     * @param $type
     * @param array $eHeaders
     * @return mixed
     */
    public function executeRequest($endpoint, $type = "GET", array $eHeaders = null)
    {
        $ch = curl_init();

        $headers = array(
            'Authorization: Basic ' . base64_encode($this->username . ":" . $this->password),
            'Cache-Control: no-cache'
        );

        if (isset($eHeaders)) {
            foreach ($eHeaders as $header) {
                array_push($headers, $header);
            }
        }

        // cURL Settings
        curl_setopt($ch, CURLOPT_URL, "https://api.stiply.nl/v1" . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (strtolower($type) == "post") {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
        } elseif (strtolower($type) == "delete") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        return curl_exec($ch);

    }

    /**
     * Create a document sign request
     * @param array $data
     * @param $type
     * @param $name
     * @param $signers
     * @param array $eHeaders
     * @return bool
     */
    public function createSignRequest(array $data, $type, $name, $signers, array $eHeaders = null)
    {
        if (isset($data, $type, $name)) {
            if ($eHeaders == null) {
                $eHeaders = array('Content-Type: multipart/form-data');
            }

            $this->data = $data;
            $request = json_decode($this->executeRequest("/sign_requests", $type, $eHeaders));
            foreach ($signers as $signer) {
                $this->addSigner($request->data->sign_request->key, $signer);
            }
            $this->sendSignrequest($request->data->sign_request->key);
            return true;
        }
        return false;
    }

    /**
     * @param string $key Sign request key
     * @return bool
     */
    public function cancelSignRequest($key)
    {
        $res = $this->executeRequest("/sign_requests/" . $key, "DELETE");
        return $res['status_code'] === 200;
    }


    /**
     * Send reminder to signer(s)
     *
     * @param string|null $key
     * @param string|null $extKey
     * @return int
     *
     */
    public function sendReminder($key = null, $extKey = null)
    {
        if (isset($key)) {
            $req = $this->executeRequest("/sign_requests/" . $key . "/actions/send_reminder");
            if ($req['status_code'] == 200) {
                return 1;
            } elseif($req['status_code'] == '400') {
                return 0;
            } elseif ($req['status_code'] == '403') {
                return 3;
            }
        } elseif (isset($extKey)) {
            if ($this->getSignRequestKeyFromExtKey($extKey) != false) {
                $key = $this->getSignRequestKeyFromExtKey($extKey);
                $request = $this->executeRequest("/sign_requests/" . $key . "/actions/send_reminder");
                if ($request['status_code'] == 200) {
                    return 1;
                } elseif($request['status_code'] == 403) {
                    return 3;
                } elseif ($request['status_code'] == 400) {
                    return 0;
                }
            }
        }
        return 2;
    }

    /**
     * Returns sign request key from external key identifier
     * @param string $extKey
     * @return bool
     */
    public function getSignRequestKeyFromExtKey($extKey)
    {
        $key = $this->executeRequest("/sign_requests/" . $extKey . "/actions/get_sign_request_key");
        if ($key['status_code'] == 200) {
            return $key['key'];
        }
        return false;
    }

    /**
     * Add a signer
     * @param $key
     * @param array|null $signers
     * @return bool|mixed
     */
    public function addSigner($key, array $signers = null)
    {
        if (isset($key, $signers)) {
            $this->data = json_encode($signers);
            $eHeaders = array('Content-Type:application/json');
            $result = $this->executeRequest("/sign_requests/" . $key . "/signers", "post", $eHeaders);
            return $result;
        }
        return false;
    }

    /**
     *
     * Sends the signrequest that has been made in the createSignRequest function
     *
     * @param $key
     * @return bool
     */
    public function sendSignrequest($key)
    {
        if (isset($key)) {
            $this->executeRequest("/sign_requests/" . $key . "/actions/send", "post");
            return true;
        }
        return false;
    }


}
