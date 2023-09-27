<?php

namespace Paysera\WalletApi\Util;

class RequestInfo
{
    /**
     * @var array
     */
    protected $server;

    /**
     * Constructs object
     *
     * @param array $server server info, usually $_SERVER
     */
    public function __construct(array $server)
    {
        $this->server = $server;
    }

    /**
     * Gets current URI without provided query parameters
     *
     *
     * @return string
     */
    public function getCurrentUri(array $removeParameters = [])
    {
        if (
            isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
        ) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $currentUri = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $parts = parse_url($currentUri);

        $port = isset($parts['port']) && (
            ($protocol === 'http://' && $parts['port'] !== 80)
            || ($protocol === 'https://' && $parts['port'] !== 443)
        ) ? ':' . $parts['port'] : '';

        if (empty($parts['query'])) {
            $query = '';
        } elseif (count($removeParameters) === 0) {
            $query = '?' . $parts['query'];
        } else {
            $queryParameters = [];
            foreach ($this->parseHttpQuery($parts['query']) as $key => $value) {
                if (!in_array($key, $removeParameters)) {
                    $queryParameters[$key] = $value;
                }
            }
            if (count($queryParameters) > 0) {
                $query = '?' . http_build_query($queryParameters, null, '&');
            } else {
                $query = '';
            }
        }

        return $protocol . $parts['host'] . $port . $parts['path'] . $query;
    }


    /**
     * Parses HTTP query to array
     *
     * @param string $query
     *
     * @return array
     */
    protected function parseHttpQuery($query)
    {
        $params = [];
        parse_str($query, $params);
        if ($this->checkMagicQuotesOption()) {
            $params = $this->stripSlashesRecursively($params);
        }

        return $params;
    }

    /**
     * Strips slashes recursively, so this method can be used on arrays with more than one level
     *
     *
     */
    protected function stripSlashesRecursively(mixed $data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[stripslashes($key)] = $this->stripSlashesRecursively($value);
            }

            return $result;
        }

        return stripslashes($data);
    }

    private function checkMagicQuotesOption()
    {
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            return false;
        }

        return get_magic_quotes_gpc();
    }
}
