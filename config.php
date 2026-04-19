<?php

$env = parse_ini_file('.env');

define('SUPABASE_URL',      $env['SUPABASE_URL']);
define('SUPABASE_ANON_KEY', $env['SUPABASE_ANON_KEY']);
define('TABLE_NAME',        $env['TABLE_NAME']);
?>