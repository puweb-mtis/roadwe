<?php
/*
|--------------------------------------------------------------------------
| 전용 언어셋입니다.
|--------------------------------------------------------------------------
| 명명규칙
| cm: 커스텀메세지
| _ + manager: 컨트롤명
| _ + fileupload: 액션(최대2뎁스까지)
| _ + 상태: success(성공), fail(실패), error(에러) 등
*/
defined('BASEPATH') or exit('No direct script access allowed');

// 공통 메세지 관련
$lang['cm_url_error'] = '비정상 접근입니다.';
$lang['cm_have_upfile_fail'] = '업로드할 파일이 존재하지 않습니다.';
$lang['cm_file_upload_fail'] = '파일 업로드에 실패 했습니다.';
$lang['cm_have_no_upfile_error'] = '업로드한 파일이 존재하지 않습니다.';
$lang['cm_upfile_allowed_extension_error'] = '유효하지 않는 파일 확장자입니다.';
$lang['cm_have_seq_data_error'] = '일련번호 데이터가 존재하지 않습니다.';
$lang['cm_have_no_data_error'] = '데이터가 존재하지 않습니다.';
$lang['cm_excel_field_set_error'] = '엑셀 다운로드 양식을 설정해주세요.';
$lang['cm_meta_group_set_error'] = '메타 그룹을 설정해주세요.';
$lang['cm_meta_create_success'] = '정상(메타) 등록 처리되었습니다.';
$lang['cm_meta_update_success'] = '정상(메타) 수정 처리되었습니다.';
$lang['cm_proc_create_success'] = '정상적으로 등록 처리되었습니다.';
$lang['cm_proc_update_success'] = '정상적으로 수정 처리되었습니다.';

// 관리자 로그인 관련
$lang['cm_admin_auth_error'] = '관리자가 아닙니다.';
$lang['cm_admin_sign_in_fail'] = '아이디, 비밀번호를 확인해주세요.';

// 관리자(운영자) 관련
$lang['cm_manager_create_success'] = '운영자 가입 정상 처리되었습니다.';
$lang['cm_manager_update_success'] = '운영자 정보를 수정하였습니다.';
$lang['cm_manager_delete_success'] = '운영자 탈퇴 처리하였습니다.';
$lang['cm_manager_create_error'] = '운영자 가입에 실패했습니다.';
$lang['cm_manager_update_error'] = '운영자 정보 수정에 실패했습니다.';
$lang['cm_manager_delete_error'] = '운영자 탈퇴 처리에 실패했습니다.';
$lang['cm_manager_search_error'] = '운영자 검색에 실패했습니다.';
$lang['cm_manager_deleteall_error'] = '운영자 전체 삭제에 실패했습니다.';
$lang['cm_manager_passwd_admin_error'] = '관리자 비밀번호를 확인하세요.';
$lang['cm_manager_passwd_matches_error'] = '비밀번호가 일치하지 않습니다.';
$lang['cm_manager_passwd_valid_error'] = '비밀번호를 입력하세요.';
$lang['cm_manager_exist_success'] = '존재하는 운영자정보 입니다.';
$lang['cm_manager_exist_error'] = '존재하지 않는 운영자입니다.';

// 운행관리 -> 차량관리 -> 차량리스트 관련
$lang['cm_vehicle_create_success'] = '차량 등록 정상 처리되었습니다.';
$lang['cm_vehicle_update_success'] = '차량 정보를 수정하였습니다.';
$lang['cm_vehicle_delete_success'] = '차량 삭제 처리하였습니다.';
$lang['cm_vehicle_create_error'] = '차량 등록에 실패했습니다.';
$lang['cm_vehicle_update_error'] = '차량 정보 수정에 실패했습니다.';
$lang['cm_vehicle_delete_error'] = '차량 삭제 처리에 실패했습니다.';
$lang['cm_vehicle_search_error'] = '차량 검색에 실패했습니다.';
$lang['cm_vehicle_deleteall_error'] = '차량 전체 삭제에 실패했습니다.';
$lang['cm_vehicle_passwd_admin_error'] = '관리자 비밀번호를 확인하세요.';
$lang['cm_vehicle_passwd_matches_error'] = '비밀번호가 일치하지 않습니다.';
$lang['cm_vehicle_passwd_valid_error'] = '비밀번호를 입력하세요.';
$lang['cm_vehicle_exist_success'] = '존재하는 차량정보 입니다.';
$lang['cm_vehicle_exist_error'] = '존재하지 않는 차량입니다.';

// 운행관리 -> 차량관리 -> 차종리스트 관련
$lang['cm_car_create_success'] = '차종 등록 정상 처리되었습니다.';
$lang['cm_car_update_success'] = '차종 정보를 수정하였습니다.';
$lang['cm_car_delete_success'] = '차종 삭제 처리하였습니다.';
$lang['cm_car_create_error'] = '차종 등록에 실패했습니다.';
$lang['cm_car_update_error'] = '차종 정보 수정에 실패했습니다.';
$lang['cm_car_delete_error'] = '차종 삭제 처리에 실패했습니다.';
$lang['cm_car_search_error'] = '차종 검색에 실패했습니다.';
$lang['cm_car_deleteall_error'] = '차종 전체 삭제에 실패했습니다.';
$lang['cm_car_passwd_admin_error'] = '관리자 비밀번호를 확인하세요.';
$lang['cm_car_passwd_matches_error'] = '비밀번호가 일치하지 않습니다.';
$lang['cm_car_passwd_valid_error'] = '비밀번호를 입력하세요.';
$lang['cm_car_exist_success'] = '존재하는 차종정보 입니다.';
$lang['cm_car_exist_error'] = '존재하지 않는 차종입니다.';

// 관리자(샘플운영자) 관련
$lang['cm_sample_create_success'] = '샘플운영자 가입 정상 처리되었습니다.';
$lang['cm_sample_update_success'] = '샘플운영자 정보를 수정하였습니다.';
$lang['cm_sample_delete_success'] = '샘플운영자 탈퇴 처리하였습니다.';
$lang['cm_sample_create_error'] = '샘플운영자 가입에 실패했습니다.';
$lang['cm_sample_update_error'] = '샘플운영자 정보 수정에 실패했습니다.';
$lang['cm_sample_delete_error'] = '샘플운영자 탈퇴 처리에 실패했습니다.';
$lang['cm_sample_search_error'] = '샘플운영자 검색에 실패했습니다.';
$lang['cm_sample_deleteall_error'] = '샘플운영자 전체 삭제에 실패했습니다.';
$lang['cm_sample_passwd_admin_error'] = '관리자 비밀번호를 확인하세요.';
$lang['cm_sample_passwd_matches_error'] = '비밀번호가 일치하지 않습니다.';
$lang['cm_sample_passwd_valid_error'] = '비밀번호를 입력하세요.';
$lang['cm_sample_exist_success'] = '존재하는 샘플운영자정보 입니다.';
$lang['cm_sample_exist_error'] = '존재하지 않는 샘플운영자입니다.';
