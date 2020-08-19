<?php

namespace App\Http;

class CustomRequests

{
  /**
   * function to make curl GET request
   *
   * @return object
   */
  public static function getRequest($url, $payload = '')
  {
      // init variable
      $retry_script = false;

      // add the payload to the url if there is any
      if ($payload) {
        $url = $url.'&'.http_build_query($payload);
      }

      // init curl
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , 7);
      curl_setopt($curl, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
      curl_setopt($curl, CURLOPT_HEADER, 0);

      // number of retries
      $retries = 0;

      // iterate
      do {
          // set this to false on every iteration
          $retry_script = false;
          // execute curl request
          $result = curl_exec($curl);
          // json decode the response
          $response = json_decode($result, true);
          // check if error found in request
          if (!isset($response['errors'])) {
              // close curl connection
              curl_close($curl);
              // return response
              return $response;
          } else {
              // check if API rate limit issue occuring
              if ($response['errors'] == "Exceeded 2 calls per second for api client. Reduce request rates to resume uninterrupted service."
                  || $response['errors'] == 'An error occurred, please try again') {
                  // sleep for 1 second
                  sleep(1);
                  // retry the script
                  $retry_script = true;
                  // increment $retries
                  $retries += 1;
              } else {
                  // close curl connection
                  curl_close($curl);
                  // return error response
                  return $response;
              }
          }

      } while ($retry_script && $retries < 50);
  }

  /**
   * function to make curl request POST
   *
   * @param string $payload
   * @return object
   */
  public static function postRequest($url, $payload = '', $access_token = '')
  {
      if ($access_token === '') {
          $headers = array('Content-Type:application/json');
      } else {
          $headers = array(
              'X-Shopify-Access-Token:'.$access_token,
              'Content-Type:application/json'
          );
      }

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_VERBOSE, 0);
      curl_setopt($curl, CURLOPT_HEADER, 0);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

      // number of retries
      $retries = 0;

      // iterate
      do {
          // set this to false on every iteration
          $retry_script = false;
          // execute curl request
          $result = curl_exec($curl);

            // json decode the response
          $response = json_decode($result, true);
          // check if error found in request
          if (!isset($response['errors'])) {
              // close curl connection
              curl_close($curl);
              // return response
              return $response;
          } else {
              // check if API rate limit issue occuring
              if ($response['errors'] == "Exceeded 2 calls per second for api client. Reduce request rates to resume uninterrupted service."
                  || $response['errors'] == 'An error occurred, please try again') {
                  // sleep for 1 second
                  sleep(1);
                  // retry the script
                  $retry_script = true;
                  // increment $retries
                  $retries += 1;
              } else {
                  // close curl connection
                  curl_close($curl);
                  // return error response
                  return $response;
              }
          }

      } while ($retry_script && $retries < 50);
  }

}
