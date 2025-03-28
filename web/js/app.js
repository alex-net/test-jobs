$(() => {
    $('body').on('init-urls', (e) => {
        $('body .url-sender').on('submit', (e) => {
            e.preventDefault();
            $.post('', $(e.target).serialize(), (ret) => {
                $(e.target).replaceWith(ret);
                $('body').trigger('init-urls');
            });
        });
    });

    $('body').trigger('init-urls');
});