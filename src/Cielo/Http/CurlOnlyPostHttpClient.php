<?php

namespace Cielo\Http;

/**
 * @author Andrey K. Vital <andreykvital@gmail.com>
 * @codeCoverageIgnoreFile
 */
final class CurlOnlyPostHttpClient implements OnlyPostHttpClientInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke($url, array $headers = [], array $fields = [])
    {
        $headers = array_map(
            function ($name, $value) {
                return sprintf('%s: %s', $name, $value);
            },
            array_keys($headers),
            array_values($headers)
        );

        $curl = curl_init((string) $url);

        curl_setopt($curl, CURLOPT_SSLVERSION, 6);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($fields));
        
        $return = curl_exec($curl);
        
        if (curl_errno($curl)) {
            throw new \Exception('Curl error: '.curl_error($curl));
        }
        return $return;
    }
}
