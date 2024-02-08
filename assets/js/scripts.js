function initFileUpload() {
    const container = $('#movie_form');
    const fileInputs = $('input[type="file"]');
    let img;
    let btn;
    const txt = 'Browse';
    const txtAfter = 'Change image';

    if (fileInputs.length < 1) {
        return;
    }

    fileInputs.each((index, input) => {
        const presentImage = $(input).data('image');
        const inputParent = $(input).parent();

        inputParent.append(`<img class="hidden custom-upload-preview mt-3" alt="Uploaded file" width="250">`);
        img = inputParent.find('img.custom-upload-preview');
        inputParent.append(`<button type="button" class="custom-upload block mt-3 py-3 px-6 bg-blue-900 text-white rounded-lg">${txt}</button>`);
        btn = inputParent.find('button.custom-upload');

        if (presentImage) {
            img.attr('src', presentImage);
            img.removeClass('hidden');
            btn.html( txtAfter );
        }

        btn.on('click', function(){
            input.click();
        });

        $(input).on('change', function(e){           
            let i = 0;
            for(i; i < e.originalEvent.srcElement.files.length; i++) {
                let file = e.originalEvent.srcElement.files[i];
                const reader = new FileReader();
    
                reader.onloadend = function(){
                    img.attr('src', reader.result);
                }
                reader.readAsDataURL(file);
                img.removeClass('hidden');
            }
            
            btn.html( txtAfter );
        });
    })
}

$(document).ready(function() {
    $('select').select2({
        width: '400px'
    });
    initFileUpload();
})