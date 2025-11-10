jQuery(document).ready(function ($) {
    $(document).on('click', '.house_and_land_link', function (e) {
        e.preventDefault();

        const $btn = $(this);
        const lotWidth = $.trim($btn.val());
        const originalHTML = $btn.html();

        if (!lotWidth) return;

        $.ajax({
            url: handl.ajax_url,
            type: 'GET',
            data: {
                action: 'house_and_land_post',
                nonce: handl.nonce,
                lotwidth: `0,${lotWidth}`,
            },
            beforeSend: function () {
                $btn
                    .prop('disabled', true)
                    .addClass('buttonload')
                    .html("<i class='fa fa-spinner fa-spin'></i> Loading...");
            },
            success: function (response) {
                if (response.success) {
                    $('.result_house_and_land').html(response.data);
                    $('html, body').animate(
                        {
                            scrollTop: $('.result_house_and_land').offset().top - 50,
                        },
                        600
                    );
                } else {
                    $('.result_house_and_land').html('<p>No results found.</p>');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Something went wrong. Please try again.');
            },
            complete: function () {
                $btn
                    .prop('disabled', false)
                    .removeClass('buttonload')
                    .html(originalHTML);
            },
        });
    });
});
