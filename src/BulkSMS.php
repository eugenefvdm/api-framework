<?php

namespace Eugenefvdm\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class BulkSMS {
    private $username;
    private $password;
    private $url;
    private $client;

    public function __construct($username, $password, ?ClientInterface $client = null) {
        $this->username = $username;
        $this->password = $password;
        $this->url = 'http://bulksms.2way.co.za/eapi/submission/send_sms/2/2.0';
        $this->client = $client ?? new Client();
    }

    /**
     * Set the HTTP client (used for testing)
     *
     * @param ClientInterface $client The HTTP client to use
     * @return void
     */
    public function setClient(ClientInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * Send SMS to multiple recipients
     * @param string $message The message to send
     * @param array|string $recipients Single number or array of numbers
     * @return array Results of sending attempts
     */
    public function sendSMS($message, $recipients) {        
        // Handle different input types for recipients
        if (is_string($recipients)) {
            // Split comma-separated string into array, and trim whitespace
            $recipients = array_map('trim', explode(',', $recipients));
        } elseif (!is_array($recipients)) {
            $recipients = [$recipients];
        }

        // Filter out empty values that might come from the .env
        $recipients = array_filter($recipients);
        
        $results = [];
        foreach ($recipients as $recipient) {
            $results[$recipient] = $this->sendToSingleRecipient($message, $recipient);
        }

        return $results;
    }

    private function sendToSingleRecipient($message, $recipient) {
        $post_body = $this->seven_bit_sms($message, $recipient);
        $result = $this->send_message($post_body);
        return $result;
    }

    private function seven_bit_sms($message, $msisdn) {
        $post_fields = array(
            'username' => $this->username,
            'password' => $this->password,
            'message'  => $this->character_resolve($message),
            'msisdn'   => $msisdn,
            'allow_concat_text_sms' => 0,
            'concat_text_sms_max_parts' => 2
        );

        return $this->make_post_body($post_fields);
    }

    private function make_post_body($post_fields) {
        $stop_dup_id = $this->make_stop_dup_id();
        if ($stop_dup_id > 0) {
            $post_fields['stop_dup_id'] = $this->make_stop_dup_id();
        }
        $post_body = '';
        foreach( $post_fields as $key => $value ) {
            $post_body .= urlencode( $key ).'='.urlencode( $value ).'&';
        }
        $post_body = rtrim( $post_body,'&' );

        return $post_body;
    }

    private function character_resolve($body) {
        $special_chrs = array(
            'Δ'=>0xD0, 'Φ'=>0xDE, 'Γ'=>0xAC, 'Λ'=>0xC2, 'Ω'=>0xDB,
            'Π'=>0xBA, 'Ψ'=>0xDD, 'Σ'=>0xCA, 'Θ'=>0xD4, 'Ξ'=>0xB1,
            '¡'=>0xA1, '£'=>0xA3, '¤'=>0xA4, '¥'=>0xA5, '§'=>0xA7,
            '¿'=>0xBF, 'Ä'=>0xC4, 'Å'=>0xC5, 'Æ'=>0xC6, 'Ç'=>0xC7,
            'É'=>0xC9, 'Ñ'=>0xD1, 'Ö'=>0xD6, 'Ø'=>0xD8, 'Ü'=>0xDC,
            'ß'=>0xDF, 'à'=>0xE0, 'ä'=>0xE4, 'å'=>0xE5, 'æ'=>0xE6,
            'è'=>0xE8, 'é'=>0xE9, 'ì'=>0xEC, 'ñ'=>0xF1, 'ò'=>0xF2,
            'ö'=>0xF6, 'ø'=>0xF8, 'ù'=>0xF9, 'ü'=>0xFC,
        );

        $ret_msg = '';
        if( mb_detect_encoding($body, 'UTF-8') != 'UTF-8' ) {
            $body = mb_convert_encoding($body, 'UTF-8', 'auto');
        }
        for ( $i = 0; $i < mb_strlen( $body, 'UTF-8' ); $i++ ) {
            $c = mb_substr( $body, $i, 1, 'UTF-8' );
            if( isset( $special_chrs[ $c ] ) ) {
                $ret_msg .= chr( $special_chrs[ $c ] );
            }
            else {
                $ret_msg .= $c;
            }
        }
        return $ret_msg;
    }

    private function make_stop_dup_id() {
        return 0;
    }

    private function send_message($post_body) {
        try {
            $response = $this->client->request('POST', $this->url, [
                'form_params' => $this->parsePostBody($post_body),
                'timeout' => 20,
                'connect_timeout' => 20
            ]);

            $response_string = (string) $response->getBody();
            $status_code = $response->getStatusCode();

            $sms_result = [
                'success' => 0,
                'details' => '',
                'http_status_code' => $status_code,
                'api_status_code' => '',
                'api_message' => '',
                'api_batch_id' => ''
            ];

            if ($status_code != 200) {
                $sms_result['details'] .= "Error: non-200 HTTP status code: " . $status_code . "\n";
            } else {
                $sms_result['details'] .= "Response from server: $response_string\n";
                $api_result = explode('|', $response_string);
                $api_status_code = $api_result[0];
                $sms_result['api_status_code'] = $api_status_code;
                $sms_result['api_message'] = $api_result[1] ?? '';

                if (count($api_result) != 3) {
                    $sms_result['details'] .= "Error: could not parse valid return data from server.\n" . count($api_result);
                } else {
                    if ($api_status_code == '0' || $api_status_code == '1') {
                        $sms_result['success'] = 1;
                        $sms_result['api_batch_id'] = $api_result[2];
                        $sms_result['details'] .= "Message sent - batch ID $api_result[2]\n";
                    } else {
                        $sms_result['details'] .= "Error sending: status code [$api_result[0]] description [$api_result[1]]\n";
                    }
                }
            }

            return $sms_result;
        } catch (\Exception $e) {
            return [
                'success' => 0,
                'details' => "Error: " . $e->getMessage() . "\n",
                'http_status_code' => 0,
                'api_status_code' => '',
                'api_message' => $e->getMessage(),
                'api_batch_id' => ''
            ];
        }
    }

    /**
     * Parse the post body string into an array
     *
     * @param string $post_body The post body string
     * @return array The parsed post body as an array
     */
    private function parsePostBody(string $post_body): array
    {
        $params = [];
        $pairs = explode('&', $post_body);
        foreach ($pairs as $pair) {
            list($key, $value) = explode('=', $pair);
            $params[urldecode($key)] = urldecode($value);
        }
        return $params;
    }
}

