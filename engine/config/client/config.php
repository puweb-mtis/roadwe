<?php defined('BASEPATH') or exit('No direct script access allowed');
$engine_config['client_title'] = '\'사이트 타이틀 강조\' - 사이트 타이틀 간략설명(파일)';
$engine_config['client_keyword'] = '사이트 키워드(파일)';
$engine_config['client_description'] = '사이트 설명(파일)';
$engine_config['client_perpage'] = 20;
if (isset($engine_config)) {
    $config['engineconf'] = $engine_config;
}
