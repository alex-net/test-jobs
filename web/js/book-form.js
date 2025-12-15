$(() => {
    $('#book-authorlist').select2({
        multiple: true,
        placeholder: 'Выбрать авторов',
        ajax: {
            url: '/authors/ajax-list',
        }
    });
});