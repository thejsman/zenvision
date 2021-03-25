<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TiktokAdController extends Controller
{
    public function store(Request $request)
    {
        $params = $request->query();
        $auth_code = $params['auth_code'];

        $PATH = "/open_api/oauth2/access_token_v2/";

        /**
         * Build request URL
         * @param $path : Request path
         * @return string
         */
        function build_url($path)
        {
            return "https://ads.tiktok.com" . $path;
        }

        /**
         * Send POST request
         * @param $json_str : Args in JSON format
         * @return bool|string : Response in JSON format
         */
        function post($json_str)
        {
            global $ACCESS_TOKEN, $PATH;
            $curl = curl_init();

            $url = build_url($PATH);

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $json_str,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                ),
            ));

            $response = curl_exec($curl);
            dd($response);
            curl_close($curl);
            return $response;
        }

        $secret = env('MIX_TIKTOK_SECRET');
        $app_id = env('MIX_TIKTOK_APP_ID');
        $auth_code = $auth_code;

        /* Args in JSON format */
        $my_args = sprintf("{\"secret\": \"%s\", \"app_id\": \"%s\", \"auth_code\": \"%s\"}", $secret, $app_id, $auth_code);
        $res = post($my_args);
        return $res;

    }
}
