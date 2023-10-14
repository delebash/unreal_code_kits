

export const editorConfiguration = {
    removePlugins: ['Title'],
    wordCount: {
        onUpdate: stats => {
            const wordCountWrapper = document.getElementById( 'word-count' );
            // Prints the current content statistics.
            wordCountWrapper.innerHTML = `Characters: ${ stats.characters }\nWords: ${ stats.words }`
            // console.log( `Characters: ${ stats.characters }\nWords: ${ stats.words }` );
        }
    },
    toolbar: {
        items: [
            'undo', 'redo',
            '|', 'heading',
            '|', 'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor',
            '|', 'bold', 'underline', 'italic', 'horizontalline', 'strikethrough', 'subscript', 'superscript', 'code', 'removeFormat',
            '|', 'link', 'blockQuote', 'codeBlock', 'uploadImage',
            '|', 'insertTable',
            '|', 'alignment', 'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent',
            '|', 'highlight', 'specialCharacters', 'findandreplace', 'selectall', 'pagebreak'
        ],
        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
        },
        shouldNotGroupWhenFull: true
    }
};

