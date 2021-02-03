// Typing in word_name will automatically fill in word_slug that replaces space and other special characters with dash

(function($){
    let slug = function(str){
        str = str.replace(/^\s+|\s+$/g, ''); // trim
        str = str.toLowerCase();

        // remove accents, swap ñ for n, etc
        let from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
        let to = "aaaaaeeeeeiiiiooooouuuunc------";
        for (let i=0,l=from.length;i<l;i++){
            str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }

        str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
            .replace(/\s+/g, '-') // collapse whitespace and replace by -
            .replace(/-+/g, '-'); // collapse dashes
        let lastChar = str.slice(-1);
        if(lastChar === '-'){
            str = str.slice(0,-1);
        }
        return str;
    };
    let wordName = $("#word_name"),
        wordSlug = $('#word_slug');
    if(wordSlug.val().length === 0){
        wordName.keyup(function(){
            let val = slug($(this).val());
            wordSlug.val(val);
        });
    }
    wordSlug.focusout(function(){
        if(wordSlug.val().length === 0) {
            let val = slug(wordName.val());
            wordSlug.val(val);
        }
    });
}(jQuery));