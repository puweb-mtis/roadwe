/**
 * 운행관리 -> 차량관리 -> 차량리스트 공통 스크립트
 */


$(function () {
    if (METHOD_NAME === 'index') { //리스트
    } else { //상세
        //차종 선택시 값 표시 처리
        $('#car_model_id_areacd').on('change', function (e) {
            if (parseInt($(this).val()) > 0) {
                let setData = {
                    'vdmd_idx': $(this).val()
                };

                let options = {
                    url : '/'+ ADMIN_URL + '/' + CONTROLLER_NAME + '/loaddata/car'
                    , data : setData  //{ 'key' : 'value', 'key' : 'value' }
                    //, dataType : 'json'
                    , wait : true
                    //, async : false
                    , callBack : function (rdata) {
                        if (rdata.result === true) {
                            let car_model_info = rdata.list;
                            $('input[name="personnel_limit"]').val(car_model_info.personnel_limit);
                            $('input[name="car_grade"]').val(car_model_info.car_type_name);
                        }
                    }
                };
                $.ajaxUtil.call(options);                    
            }
        });

        //운송회사 선택시 값 표시 처리
        $('#transport_id_areacd').on('change', function (e) {
            if (parseInt($(this).val()) > 0) {
                let setData = {
                    'tpcp_idx': $(this).val()
                };

                let options = {
                    url : '/'+ ADMIN_URL + '/' + CONTROLLER_NAME + '/loaddata/transport'
                    , data : setData  //{ 'key' : 'value', 'key' : 'value' }
                    //, dataType : 'json'
                    , wait : true
                    //, async : false
                    , callBack : function (rdata) {
                        if (rdata.result === true) {
                            let transport_company_info = rdata.list;
                            $('input[name="transport_phone"]').val(transport_company_info.phone);
                            $('input[name="transport_manager_name"]').val(transport_company_info.manager_name);
                            $('input[name="transport_manager_mobile_number"]').val(transport_company_info.manager_mobile_number);                                
                        }
                    }
                };
                $.ajaxUtil.call(options);
            }
        });            
        
        //운행기사님 선택시 값 표시 처리
        $('#driver_id_areacd').on('change', function (e) {
            if (parseInt($(this).val()) > 0) {
                let setData = {
                    'vddv_idx': $(this).val()
                };

                let options = {
                    url : '/'+ ADMIN_URL + '/' + CONTROLLER_NAME + '/loaddata/driver'
                    , data : setData  //{ 'key' : 'value', 'key' : 'value' }
                    //, dataType : 'json'
                    , wait : true
                    //, async : false
                    , callBack : function (rdata) {
                        if (rdata.result === true) {
                            let driver_info = rdata.list;
                            $('input[name="driver_transport_name"]').val(driver_info.company_name);
                            $('input[name="driver_company_name"]').val(driver_info.company_name);
                            $('input[name="driver_phone"]').val(driver_info.phone);
                            $('input[name="driver_manager_name"]').val(driver_info.manager_name);
                            $('input[name="driver_manager_mobile_number"]').val(driver_info.manager_mobile_number);
                            
                        }
                    }
                };
                $.ajaxUtil.call(options);
            }
        });

        if (crud_mode === 'create') {  
        } else if (crud_mode === 'update') {
            $('#car_model_id_areacd').trigger('change');
            $('#transport_id_areacd').trigger('change');
            $('#driver_id_areacd').trigger('change');
        }
    }
});

function addFileImage(target) {
    const $list = document.querySelector(target);
    const $columns = $list.querySelectorAll('.col-auto');
    const $column = document.createElement('div');
    if ( $columns.length < 10 ) {
        $column.classList.add('col-auto');
        $column.innerHTML = '<label class="file-image"><input type="file" name="car_image[]" accept="image/gif, image/jpeg, image/png"><div class="file-image-preview"><img src="/builds/admin/_theme/velzon/assets/images/blank.svg" alt=""></div><button type="button" class="btn btn-danger btn-sm w-100 mt-1 d-none" data-btn-remove>파일삭제</button><button type="button" class="btn btn-dark btn-sm w-100 mt-1" data-btn-sample>샘플</button></label>';
        $list.append($column);
    } else {
        alert('최대 10개까지 첨부 가능합니다.');
    }
}