/**
 * 플랫폼 계정관리 -> 계정설정 공통 스크립트
 */
$(function () {
    if (METHOD_NAME === 'index') { //리스트
    } else { //상세
        if (crud_mode === 'create') {
            $('#acmg_frm').is(function () {
                $('#acmg_frm').validate({
                    ignore: "not:hidden",
                    rules: {
                        password: {
                            required: true
                        },
                        re_password: {
                            required: true,
                            equalTo: password
                        },
                        tel: {
                            required: true,
                            mobilephoneKO: true
                        },
                        tel_did: {
                            required: true,
                            mobilephoneKO: true
                        },                    
                        phone: {
                            required: true,
                            mobilephoneKO: true
                        },
                    },
                    messages: {
                        password: {
                            required: '를 입력하세요.',
                        },
                        re_password: {
                            required: '를 입력하세요.',
                        },
                        tel: {
                            required: '를 입력하세요.',
                        },
                        tel_did: {
                            required: '를 입력하세요.',
                        },                    
                        phone: {
                            required: '를 입력하세요.',
                        },                    
                    }
                });
            });            
        }   
    }
});