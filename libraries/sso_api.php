<?php
# copy of rev 447

// General technical exceptions (for production, only concentrate on the first)
class AuthenticationServerException extends Exception { }
class ErrorContactingAuthenticationServerException extends AuthenticationServerException { }
class AuthenticationServerReturnsErrorException extends AuthenticationServerException { }
class ErrorParsingResponseException extends AuthenticationServerException { }

// Authenticate exceptions
class AuthWrongCredentialsException extends Exception { }
class AuthUserInactiveException extends Exception { }
class AuthMalformedInputException extends Exception { }
class AuthUnknownException extends Exception { }

// Info exceptions
class InfoNoAuthenticatedUserException extends Exception { }


class SelvbetjeningIntegrationSSO {

    public function authenticate($username, $password) {
        $url = SELV_API_URL . "authenticate/" . SELV_SERVICE_ID . "/";
        $response = $this->call($url, array("username" => $username, "password" => $password));

        try {
            @$xml = new SimpleXMLElement($response);

            if ($xml->success == "True") {
                setcookie(SELV_AUTH_TOKEN_KEY, $xml->session->auth_token, (int) $xml->session->expire, $xml->session->path, $xml->session->domain);
                $_COOKIE[SELV_AUTH_TOKEN_KEY] = $xml->session->auth_token; // save value if we need it before browser refresh

                return $this->user_xml_to_array($xml->user);

            }

            $error_code = (string) $xml->error_code;

        } catch (Exception $e) {
            throw new ErrorParsingResponseException();
        }

        switch ($error_code) {
            case "auth_wrong_credentials":
                throw new AuthWrongCredentialsException();
            case "auth_user_inactive":
                throw new AuthUserInactiveException();
            case "auth_malformed_input":
                throw new AuthMalformedInputException();
            default:
                throw new AuthUnknownException();
        }
    }

    public function is_authenticated() {
        $auth_token = $this->get_auth_token();

        if ($auth_token === false) {
            return false;
        }

        $url = SELV_API_URL . "validate/" . SELV_SERVICE_ID . "/" . $auth_token . "/";
        $response = $this->call($url);

        if (strrpos($response, "accepted") === false) {
            return false;
        } else {
            return substr($response, strlen("accepted/"));
        }
    }

    public function get_session_info() {
        $auth_token = $this->get_auth_token();

        if ($auth_token === false) {
            throw new InfoNoAuthenticatedUserException();
        }

        $url = SELV_API_URL . "info/" . SELV_SERVICE_ID . "/" . $auth_token . "/";
        $response = $this->call($url);

        try {
            @$xml = new SimpleXMLElement($response);
            $success = (string) $xml->success;

        } catch (Exception $e) {
            throw new ErrorParsingResponseException();
        }

        if ($success == "False") {
            throw new InfoNoAuthenticatedUserException();
        } else {
            try {
                return $this->user_xml_to_array($xml->user);
            } catch (Exception $e) {
                throw new ErrorParsingResponseException();
            }
        }
    }

    protected function call($url, $post_data=False) {
        try {
            $ch = curl_init($url);

            if ($post_data !== False) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            }

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } catch (Exception $e) {
            throw new ErrorContactingAuthenticationServerException();
        }

        if ($status_code !== 200) {
            throw new AuthenticationServerReturnsErrorException();
        }

        return $response;
    }

    protected function get_auth_token() {
        $auth_token = isset($_COOKIE[SELV_AUTH_TOKEN_KEY]) ? $_COOKIE[SELV_AUTH_TOKEN_KEY] : false;
        return $auth_token != "" ? $auth_token : false;
    }

    protected function user_xml_to_array($user_xml_part) {
        $groups = array();

        $groups_raw = $user_xml_part->groups->children();
        if (@count($groups_raw) > 0) {
            foreach ($groups_raw as $group) {
                $groups[] = (string) $group;
            }
        }


        return array("username" => (string) $user_xml_part->username,
                     "last_name" => (string) $user_xml_part->last_name,
                     "first_name" => (string) $user_xml_part->first_name,
                     "email" => (string) $user_xml_part->email,
                     "date_joined" => (string) $user_xml_part->date_joined,
                     "groups" => $groups);
    }

}
