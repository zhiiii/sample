<?php

function get_db_config()
{
    if (getenv('IS_IN_HEROKU')) {
        $url = parse_url(getenv('DATABASE_URL'));

        return $db_config = [
            'connection' => 'pgsql',
            'host' => $url['host'],
            'database' => substr($url['path'], 1),
            'username' => $url['user'],
            'password' => $url['pass']
        ];
    } else {
        return $db_config = [
            'connection' => nev('DB_CONNECTION', 'mysql'),
            'host ' => nev('DB_HOST', 'localhost'),
            'database' => nev('DB_DATABASE', 'sample'),
            'username' => nev('DB_USERNAME', 'homestead'),
            'password' => nev('DB_PASSWORD', 'secret')
        ];
    }

}