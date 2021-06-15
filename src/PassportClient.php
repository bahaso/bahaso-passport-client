<?php
/**
 * Created by PhpStorm.
 * User: bahasolaptop2
 * Date: 30/07/19
 * Time: 15:07
 */

namespace Bahaso\PassportClient;


use Bahaso\PassportClient\Entities\User;
use Bahaso\PassportClient\Exceptions\InvalidGrantTypeException;
use Bahaso\PassportClient\Exceptions\InvalidRequestException;
use Bahaso\PassportClient\Exceptions\ServerResponseException;
use Bahaso\PassportClient\Requests\Contracts\PassportRequest;
use Bahaso\PassportClient\Requests\SignInRequest;
use Bahaso\PassportClient\Requests\SignUpRequest;
use Bahaso\PassportClient\Requests\SocialAuthRequest;
use Bahaso\PassportClient\Responses\GetUserResponse;
use Bahaso\PassportClient\Responses\Response;
use Bahaso\PassportClient\Responses\SignInResponse;
use Illuminate\Support\Facades\Cache;

class PassportClient
{
    const GRANT_TYPE_PASSWORD = "password";
    const GRANT_TYPE_SOCIAL = "social";
    const FACEBOOK_PROVIDER = "facebook";
    const GOOGLE_PROVIDER = "google";

    private static $user = null;
    private static $access_token = '';

    protected function prepareHttpClient()
    {
        $client = new \GuzzleHttp\Client([
            'verify' => false
        ]);

        return $client;
    }

    private function prepareRequestHeader()
    {
        return [
            'headers' => [
                'Accept' => 'application/json'
            ]
        ];
    }

    public function testConnection()
    {
        $client = $this->prepareHttpClient();

        $response = $client->get(config('passport.test_connection_url', 'https://passporization.dev.io/api/connection/test'));

        if ($response->getStatusCode() == 200) {
            return new Response($response->getStatusCode(), true, "You are connected with Auth Server", json_decode($response->getBody()));
        } else {
            throw new ServerResponseException(500, "Something wrong with Authorization Server");
        }
    }

    public function prepareSignInRequest($client_id, $client_secret, $username, $password, $grant_type, $scope)
    {
        return
            new SignInRequest(
                $client_id,
                $client_secret,
                $username,
                $password,
                $grant_type,
                $scope
            );
    }

    public function signIn(SignInRequest $request)
    {
        $http = $this->prepareHttpClient();
        $body = $this->prepareRequestBody($request);
        $headers = $this->prepareRequestHeader();
        $options = array_merge($body, $headers);

        try {
            $request = $http->request(
                'post',
                config('passport.sign_in_url', ''),
                $options);
        } catch (\Exception $exception) {
            throw new ServerResponseException($exception->getCode(), $exception->getMessage());
        }

        $response = json_decode((string)$request->getBody(), true);

        return $this->handleAuthServerSignInResponse($response);
    }

    private function handleAuthServerSignInResponse($response)
    {
        if ($response['status'] !== 200) {
            $this->handleAuthServerResponseException($response);
        }
        return new SignInResponse($response['status'], true, $response['message'], $response['data']);
    }

    public function prepareSignUpRequest(
        $fullname,
        $email,
        $password,
        $client_id,
        $client_secret,
        $grant_type,
        $scope
    )
    {
        return new SignUpRequest(
            $fullname,
            $email,
            $password,
            $client_id,
            $client_secret,
            $grant_type,
            $scope
        );
    }

    public function signUp(SignUpRequest $request)
    {
        $http = $this->prepareHttpClient();
        $body = $this->prepareRequestBody($request);
        $headers = $this->prepareRequestHeader();
        $options = array_merge($body, $headers);

        try {
            $request = $http->request(
                'post',
                config('passport.sign_up_url', ''),
                $options);
        } catch (\Exception $exception) {
            throw new ServerResponseException($exception->getCode(), $exception->getMessage());
        }

        $response = json_decode((string)$request->getBody(), true);

        return $this->handleAuthServerSignInResponse($response);
    }

    private function prepareRequestBody(PassportRequest $request)
    {
        if ($request->getGrantType() == self::GRANT_TYPE_PASSWORD || $request->getGrantType() == self::GRANT_TYPE_SOCIAL) {
            return [
                "form_params" => (array)$request
            ];
        } else {
            throw new InvalidGrantTypeException(422, "Grant type " . $request->getGrantType() . " is not supported");
        }
    }

    private function handleAuthServerResponseException($response)
    {
        $errors = isset($response['errors']) ? $response['errors'] : [];
        throw new ServerResponseException($response['status'], $response['message'], $errors);
    }

    public function validateAccessToken($access_token = null)
    {
        if (!$access_token) $access_token = $this->getAccessTokenFromHeader();
        return $this->getUserFromToken($access_token);
    }

    public function getAccessTokenFromHeader()
    {
        $header = request()->header('Authorization');
        if (!$header) throw new InvalidRequestException(422, "Missing Authorization on header request");
        if (strpos("Bearer", $header) !== false) throw new InvalidRequestException(422, "Missing Bearer on header request");
        return explode(" ", $header)[1];
    }

    public function getUserFromToken($access_token)
    {
        $http = $this->prepareHttpClient();
        $headers = $this->prepareRequestHeaderWithToken($access_token);
        $options = array_merge($headers);

        try {
            $request = $http->request(
                'get',
                config('passport.get_user_from_token_url', ''),
                $options);
        } catch (\Exception $exception) {
            throw new ServerResponseException($exception->getCode(), $exception->getMessage());
        }

        $response = json_decode((string)$request->getBody(), true);

        return $this->handleAuthServerGetUserResponse($response);
    }

    private function prepareRequestHeaderWithToken($access_token)
    {
        return [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $access_token
            ]
        ];
    }

    private function handleAuthServerGetUserResponse($response)
    {
        if ($response['status'] !== 200) {
            $this->handleAuthServerResponseException($response);
        }

        return new GetUserResponse($response['status'], true, $response['message'], $response['data']);
    }

    private function handleAuthServerCheckTokenResponse($access_token, $scope, $response)
    {
        if ($response['status'] !== 200) {
            $this->handleAuthServerResponseException($response);
        }

        $user = $response['data']['user'];
        self::$user = new User($user['_id'], $user['username'], $user['fullname'], $user['email']);

        Cache::put($access_token, self::$user, 600);
        Cache::put($access_token . '_scope', $scope, 600);

        return new GetUserResponse($response['status'], true, $response['message'], $response['data']['oauth']);
    }

    public function prepareSocialAuthRequest($access_token, $client_id, $client_secret, $grant_type, $scope)
    {
        return
            new SocialAuthRequest(
                $access_token,
                $client_id,
                $client_secret,
                $grant_type,
                $scope
            );
    }

    public function socialAuth(SocialAuthRequest $request, $provider)
    {
        $http = $this->prepareHttpClient();
        $body = $this->prepareRequestBody($request);
        $headers = $this->prepareRequestHeader();
        $options = array_merge($body, $headers);

        if ($provider == self::FACEBOOK_PROVIDER)
            $url = config('passport.facebook_auth_url', '');
        else if ($provider == self::GOOGLE_PROVIDER)
            $url = config('passport.google_auth_url', '');
        else
            $url = "notfound";

        try {
            $request = $http->request(
                'post',
                $url,
                $options);
        } catch (\Exception $exception) {
            throw new ServerResponseException($exception->getCode(), $exception->getMessage());
        }

        $response = json_decode((string)$request->getBody(), true);

        return $this->handleAuthServerSignInResponse($response);
    }

    public function checkToken($access_token, $scope)
    {
        self::$access_token = $access_token;

        if (Cache::has($access_token) && Cache::has($access_token . '_scope')) {
            $array_saved_scopes = explode(',', Cache::get($access_token . '_scope'));

            $array_check_scopes = explode(',', $scope);

            foreach ($array_check_scopes as $scope) {
                if (!in_array($scope, $array_saved_scopes))
                    throw new ServerResponseException(401, 'you are not authorized');
            }

            self::$user = Cache::get($access_token);
            return new GetUserResponse(200, true, 'success', self::$user);
        }

        $http = $this->prepareHttpClient();
        $headers = $this->prepareRequestHeaderWithToken($access_token);
        $options = array_merge($headers);

        try {
            $request = $http->request(
                'get',
                config('passport.check_token_url', '') . '?scope=' . $scope,
                $options);
        } catch (\Exception $exception) {
            throw new ServerResponseException($exception->getCode(), $exception->getMessage());
        }

        $response = json_decode((string)$request->getBody(), true);

        return $this->handleAuthServerCheckTokenResponse($access_token, $scope, $response);
    }

    /**
     * @return User
     */
    public static function user()
    {
        return self::$user;
    }
}
