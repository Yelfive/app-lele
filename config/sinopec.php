<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-04-20
 */

/*
 * usrCode=becdc2d42a933f0,
 * 'usrName'=>'lili',
 * 'token'=>'bGlsaTE0OTE1NTA2NTAuNDE3NQ=='
 */
return [
    'host_api' => env('SINOPEC_API_HOST', 'http://openapi.indoorbuy.com/api/Interface/indoorbuy'),
    'host_file' => env('SINOPEC_FILE_HOST', 'http://openapi.indoorbuy.com/Api/Interface/uploadIdcard.html'),
    'user_code' => env('SINOPEC_API_USER_CODE', 'becdc2d42a933f0'),
    'user_name' => env('SINOPEC_API_USER_NAME', 'lili'),
    'token' => env('SINOPEC_API_TOKEN', 'bGlsaTE0OTE1NTA2NTAuNDE3NQ=='),
    'encrypt_cypher' => env('SINOPEC_API_CYPHER', '1234567890123456'),
    'encrypt_method' => 'aes-128-ecb',
];