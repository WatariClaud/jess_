<?php

    class token_auth {
            
        private function base64url_encode($str) {
            return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
        }
        
        function is_jwt_valid($jwt, $secret = 'jess-app') {
            $tokenParts = explode('.', $jwt);
            $header = base64_decode($tokenParts[0]);
            $payload = base64_decode($tokenParts[1]);
            $signature_provided = $tokenParts[2];
        
            $base64_url_header = $this->base64url_encode($header);
            $base64_url_payload =  $this->base64url_encode($payload);
            $signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
            $base64_url_signature =  $this->base64url_encode($signature);
        
            $is_signature_valid = ($base64_url_signature === $signature_provided);

            $unencodedData = (array) $payload;
            $unencodedData_ = $unencodedData[0];
            
            $user_id = json_decode($unencodedData_)->user;
            
            if (!$is_signature_valid) {
                return FALSE;
            } else {
                return $user_id;
            }
        }

    };