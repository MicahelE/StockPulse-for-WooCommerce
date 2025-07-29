jQuery(document).ready(function($) {
    $('#stockpulse-notify-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitBtn = form.find('.stockpulse-submit-btn');
        var messageDiv = form.find('.stockpulse-message');
        var email = form.find('#stockpulse-email').val();
        var productId = form.find('input[name="product_id"]').val();
        
        submitBtn.prop('disabled', true).text(stockpulse_ajax.submitting || 'Submitting...');
        messageDiv.hide().removeClass('success error');
        
        $.ajax({
            url: stockpulse_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'stockpulse_subscribe',
                email: email,
                product_id: productId,
                nonce: stockpulse_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    messageDiv.addClass('success').text(response.data.message).fadeIn();
                    form.find('#stockpulse-email').val('');
                } else {
                    messageDiv.addClass('error').text(response.data.message).fadeIn();
                }
            },
            error: function() {
                messageDiv.addClass('error').text('An error occurred. Please try again.').fadeIn();
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Notify Me');
            }
        });
    });
});