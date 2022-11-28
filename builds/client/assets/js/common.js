const $sidebar = document.querySelector('.sidebar');
const $navOpenButton = document.querySelector('.header .btn-mo-nav');
const $navCloseButton = document.querySelector('.sidebar .btn-mo-nav');
const $sidebarNavItems = document.querySelectorAll('.sidebar .nav .nav-list__item a');

$sidebarNavItems.forEach($navItem => {
    $navItem.addEventListener('click', e => {
        const targetName = e.target.hash.replace('#', '');
        const $section = document.querySelector(`[data-mo-anchor="${targetName}"]`);
        
        if ( $section ) {
            e.preventDefault();
            
            window.scrollTo({
                top: $section.offsetTop,
                behavior: 'smooth'
            });
    
            $sidebar.classList.remove('active');
            document.body.classList.remove('nav-opened');
        }
    });
});

$navOpenButton.addEventListener('click', e => {
    $sidebar.classList.add('active');
    document.body.classList.add('nav-opened');
});

$navCloseButton.addEventListener('click', e => {
    $sidebar.classList.remove('active');
    document.body.classList.remove('nav-opened');
});

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