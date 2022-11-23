const $letterWraps = document.querySelectorAll('.letter-wrap');
const letterWrapCreateLetter = ($letterWrap) => {
    if ( !$letterWrap.querySelectorAll('.letter').length ) {
        const letterArray = $letterWrap.textContent.split('');
    
        $letterWrap.innerHTML = '';
    
        for (let i = 0; i < letterArray.length; i++) {
            if ( letterArray[i] !== ' ' ) {
                $letterWrap.innerHTML += `<span class="letter">${letterArray[i]}</span>`;
            } else {
                $letterWrap.innerHTML += ' ';
            }
        }
    }
}
const letterWrapCreateWord = ($letterWrap) => {
    if ( !$letterWrap.querySelectorAll('.letter').length ) {
        const letterArray = $letterWrap.textContent.split(' ');
    
        $letterWrap.innerHTML = '';
    
        for (let i = 0; i < letterArray.length; i++) {
            if ( i !== 0 ) {
                $letterWrap.innerHTML += ` <span class="letter">${letterArray[i]}</span>`;
            } else {
                $letterWrap.innerHTML += `<span class="letter">${letterArray[i]}</span>`;
            }
        }
    }
}

$letterWraps.forEach($letterWrap => {
    if ( $letterWrap.getAttribute('data-letter-wrap') === 'letter' ) {
        letterWrapCreateLetter($letterWrap);
    } else {
        letterWrapCreateWord($letterWrap);
    }
});