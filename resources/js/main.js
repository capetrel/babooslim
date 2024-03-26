import '../sass/main.scss'

let flash = document.getElementById('flashMessage');
if(flash) {
    let close = document.getElementById('closeFlash');
    close.addEventListener('click', function (e) {
        e.preventDefault();
        removeFlashMessage(flash);
    });

    window.setTimeout(function(handler){
        removeFlashMessage(flash);
    }, 7000)
}

function removeFlashMessage(element) {
    let parent = element.parentNode;
    parent.removeChild(element);
}