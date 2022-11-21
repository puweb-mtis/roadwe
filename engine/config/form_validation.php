<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| 폼검증 조건 추가
|--------------------------------------------------------------------------
|
| admin_sign_in: 관리자 인증 로그인 용
*/
$config = array();
$config['admin_sign_in'] = array(
    array(
        'field' => 'acmg_id',
        'label' => '관리자 아이디',
        'rules' => 'required'
    ),
    array(
        'field' => 'acmg_pw',
        'label' => '관리자 비밀번호',
        'rules' => 'required',
    ),
);

// 플랫폼 계정관리 -> 계정설정 등록
$config['manager_create'] = array(
    array(
        'field' => 'business_team_id',
        'label' => '근무부서',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'position',
        'label' => '직책',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'class',
        'label' => '직급',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'account',
        'label' => '아이디(이메일)',
        'rules' => 'trim|required|valid_email',
    ),
    array(
        'field' => 'password',
        'label' => '비밀번호',
        'rules' => 'trim|required',
    ),
    array(
        'field' => 're_password',
        'label' => '비밀번호 확인',
        'rules' => 'trim|required|matches[password]',
    ),
    // array(
    //     'field' => 'tel',
    //     'label' => '연락처',
    //     'rules' => 'required|regex_match[/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/]',
    // ),
    // array(
    //     'field' => 'tel_did',
    //     'label' => '내선번호',
    //     'rules' => 'required|regex_match[/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/]',
    // ),
    // array(
    //     'field' => 'phone',
    //     'label' => '휴대폰번호',
    //     'rules' => 'required|regex_match[/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/]',
    // )
);

// 플랫폼 계정관리 -> 계정설정 수정
$config['manager_update'] = array(
    array(
        'field' => 'business_team_id',
        'label' => '근무부서',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'position',
        'label' => '직책',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'class',
        'label' => '직급',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'account',
        'label' => '아이디(이메일)',
        'rules' => 'trim|required|valid_email',
    ),
    array(
        'field' => 'password',
        'label' => '비밀번호',
        'rules' => 'trim|required',
    ),
    array(
        'field' => 're_password',
        'label' => '비밀번호 확인',
        'rules' => 'trim|required|matches[password]',
    ),
    // array(
    //     'field' => 'tel',
    //     'label' => '연락처',
    //     'rules' => 'required|regex_match[/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/]',
    // ),
    // array(
    //     'field' => 'tel_did',
    //     'label' => '내선번호',
    //     'rules' => 'required|regex_match[/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/]',
    // ),
    // array(
    //     'field' => 'phone',
    //     'label' => '휴대폰번호',
    //     'rules' => 'required|regex_match[/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/]',
    // )
);

// 운행관리 -> 차량관리 -> 차량리스트 등록
$config['vehicle_create'] = array(
    array(
        'field' => 'device_unit_id',
        'label' => '단말기 고유 ID',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'car_name',
        'label' => '차량명',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'car_number',
        'label' => '차량번호',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'car_model_id',
        'label' => '차종',
        'rules' => 'trim|required'
    )
);

// 운행관리 -> 차량관리 -> 차량리스트 수정
$config['vehicle_update'] = array(
    array(
        'field' => 'device_unit_id',
        'label' => '단말기 고유 ID',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'car_name',
        'label' => '차량명',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'car_number',
        'label' => '차량번호',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'car_model_id',
        'label' => '차종',
        'rules' => 'trim|required'
    )
);

// 운행관리 -> 차량관리 -> 차종리스트 등록
$config['car_create'] = array(
    array(
        'field' => 'car_type_id',
        'label' => '종류',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'car_producer_id',
        'label' => '제조사',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'car_name',
        'label' => '차종',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'personnel_limit',
        'label' => '탑승가능 인원수',
        'rules' => 'trim|required'
    )
);

// 운행관리 -> 차량관리 -> 차종리스트 수정
$config['car_update'] = array(
    array(
        'field' => 'car_type_id',
        'label' => '종류',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'car_producer_id',
        'label' => '제조사',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'car_name',
        'label' => '차종',
        'rules' => 'trim|required'
    ),
    array(
        'field' => 'personnel_limit',
        'label' => '탑승가능 인원수',
        'rules' => 'trim|required'
    )
);

/*
* 샘플용
* ************************************************************************/
// 샘플운영자 회원가입
$config['sample_create'] = array(
    array(
        'field' => 'spmg_id',
        'label' => '샘플운영자 아이디',
        'rules' => 'required'
    ),
    array(
        'field' => 'spmg_name',
        'label' => '샘플운영자 성명',
        'rules' => 'required'
    ),
    array(
        'field' => 'spmg_pw',
        'label' => '관리자 비밀번호',
        'rules' => 'required|matches[spmg_re_pw]',
    ),
    array(
        'field' => 'spmg_re_pw',
        'label' => '관리자 비밀번호 확인',
        'rules' => 'required',
    ),
);

// 샘플운영자 회원수정
$config['sample_update'] = array(
    array(
        'field' => 'spmg_id',
        'label' => '샘플운영자 아이디',
        'rules' => 'required'
    ),
    array(
        'field' => 'spmg_name',
        'label' => '샘플운영자 성명',
        'rules' => 'required'
    )
);

// 일반회원 회원가입
$config['member_create'] = array(
    array(
        'field' => 'account',
        'label' => '이메일',
        'rules' => 'required|is_unique[member.account]'
    ),
    array(
        'field' => 'password',
        'label' => '비밀번호',
        'rules' => 'required|matches[re_password]'
    ),
    array(
        'field' => 'password',
        'label' => '비밀번호 확인',
        'rules' => 'required'
    ),
    array(
        'field' => 'phone',
        'label' => '휴대폰번호',
        'rules' => 'required'
    )
);

// 일반회원 회원수정
$config['member_update'] = array(
    array(
        'field' => 'account',
        'label' => '이메일',
        'rules' => 'required'
    ),
    array(
        'field' => 'phone',
        'label' => '휴대폰번호',
        'rules' => 'required'
    )
);
