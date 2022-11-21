<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @todo 참조내용.
 * 주) 하위메뉴가 있는경우 link는 하위메뉴중에 대표 링크 주소를 적어준다.
 */
// 관리자 플랫폼 관리 메뉴 지정
$config['admin_menu'][100] = array(
    'title' => '플랫폼 관리',
    'link' => 'manager/index',
    'icon' => 'ti-user',
    'page_title' => '플랫폼 관리 타이틀',
    'page_desc' => '플랫폼 관리 설명'
);
$config['admin_menu'][100]['child'] = array(
    '110' => array(
        'title' => '플랫폼 운영관리',
        'link' => 'running/index',
        'page_title' => '플랫폼 운영관리 타이틀',
        'page_desc' => '플랫폼 운영관리 설명'
    ),
    '120' => array(
        'title' => '플랫폼 계정관리',
        'link' => 'manager/index',
        'page_title' => '플랫폼 계정관리 타이틀',
        'page_desc' => '플랫폼 계정관리 설명'
    ),
);
// 3차 메뉴 사용 시 지정 방법
$config['admin_menu'][100]['child'][110]['child'] = array(
    '111' => array(
        'title' => '기본정보설정',
        'link' => 'basic/index',
        'page_title' => '기본정보설정 타이틀',
        'page_desc' => '기본정보설정 설명'
    ),
    '112' => array(
        'title' => '이용약관설정',
        'link' => 'basic/index',
        'page_title' => '이용약관설정 타이틀',
        'page_desc' => '이용약관설정 설명'
    )
);
$config['admin_menu'][100]['child'][120]['child'] = array(
    '121' => array(
        'title' => '계정설정',
        'link' => 'manager/index',
        'page_title' => '계정설정 타이틀',
        'page_desc' => '계정설정 설명'
    ),
    '122' => array(
        'title' => '근무부서설정',
        'link' => 'basic/index',
        'page_title' => '근무부서설정 타이틀',
        'page_desc' => '근무부서설정 설명'
    )
);
// 관리자 회원 관리 메뉴 지정
$config['admin_menu'][200] = array(
    'title' => '회원관리',
    'link' => 'member/index',
    'icon' => 'ti-write',
    'page_title' => '회원관리 타이틀',
    'page_desc' => '회원관리 설명'
);
$config['admin_menu'][200]['child'] = array(
    '210' => array(
        'title' => '회원리스트',
        'link' => 'member/index',
        'page_title' => '회원리스트 타이틀',
        'page_desc' => '회원리스트 설명'
    ),
    '220' => array(
        'title' => '탈퇴회원',
        'link' => 'withdraw/index',
        'page_title' => '탈퇴회원 타이틀',
        'page_desc' => '탈퇴회원 설명'
    ),
    '230' => array(
        'title' => '휴면회원',
        'link' => 'quiescency/index',
        'page_title' => '휴면회원 타이틀',
        'page_desc' => '휴면회원 설명'
    )
);

// 관리자 고객사 관리 메뉴 지정
$config['admin_menu'][300] = array(
    'title' => '고객사관리',
    'link' => 'transportcompany/index',
    'icon' => 'ti-user',
    'page_title' => '고객사관리 타이틀',
    'page_desc' => '고객사관리 설명'
);
$config['admin_menu'][300]['child'] = array(
    '310' => array(
        'title' => '운송회사리스트',
        'link' => 'transportcompany/index',
        'page_title' => '운송회사리스트 타이틀',
        'page_desc' => '운송회사리스트 설명'
    ),
    '320' => array(
        'title' => '거래처리스트',
        'link' => 'company/index',
        'page_title' => '거래처리스트 타이틀',
        'page_desc' => '거래처리스트 설명'
    ),
);

// 관리자 운행 관리 메뉴 지정
$config['admin_menu'][400] = array(
    'title' => '운행관리',
    'link' => 'manager/index',
    'icon' => 'ti-user',
    'page_title' => '운행관리 타이틀',
    'page_desc' => '운행관리 설명'
);
$config['admin_menu'][400]['child'] = array(
    '410' => array(
        'title' => '차량관리',
        'link' => 'running/index',
        'page_title' => '차량관리 타이틀',
        'page_desc' => '차량관리 설명'
    ),
    '420' => array(
        'title' => '운행노선(정류장)관리',
        'link' => 'runroute/index',
        'page_title' => '운행노선(정류장)관리 타이틀',
        'page_desc' => '운행노선(정류장)관리 설명'
    ),
);
// 3차 메뉴 사용 시 지정 방법
$config['admin_menu'][400]['child'][410]['child'] = array(
    '411' => array(
        'title' => '차량리스트',
        'link' => 'vehicle/index',
        'page_title' => '차량리스트 타이틀',
        'page_desc' => '차량리스트 설명'
    ),
    '412' => array(
        'title' => '차종리스트',
        'link' => 'car/index',
        'page_title' => '차종리스트 타이틀',
        'page_desc' => '차종리스트 설명'
    )
);
$config['admin_menu'][400]['child'][420]['child'] = array(
    '421' => array(
        'title' => '노선리스트',
        'link' => 'runroute/index',
        'page_title' => '노선리스트 타이틀',
        'page_desc' => '노선리스트 설명'
    ),
    '422' => array(
        'title' => '배차리스트',
        'link' => 'vehicleallocation/index',
        'page_title' => '배차리스트 타이틀',
        'page_desc' => '배차리스트 설명'
    )
);
