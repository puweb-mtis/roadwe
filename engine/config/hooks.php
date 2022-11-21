<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/userguide3/general/hooks.html
|
*/
// 시스템 작동초기입니다.벤치마크와 후킹클래스들만 로드된 상태로서, 라우팅을 비롯한 어떤 다른 프로세스도 진행되지않은 상태입니다.
$hook['pre_system'] = function () {
    $ST = &load_class('Siteinit', 'hooks');
    $ST->configure();
};

// 컨트롤러가 호출되기 직전입니다. 모든 기반클래스(base classes), 라우팅 그리고 보안점검이 완료된 상태입니다.
$hook['pre_controller'] = function () {
    $ST = &load_class('Siteinit');
    $ST->initialize();
};
