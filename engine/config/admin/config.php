<?php defined('BASEPATH') or exit('No direct script access allowed');
$engine_config['admin_title'] = '관리자 타이틀(파일)';
$engine_config['admin_keyword'] = '관리자 키워드(파일)';
$engine_config['admin_description'] = '관리자 설명(파일)';
$engine_config['admin_perpage'] = 20;
$engine_config['admin_auth_title'] = '관리자 로그인 타이틀(파일)';
if (isset($engine_config)) {
    $config['engineconf'] = $engine_config;
}
