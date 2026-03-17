jQuery(document).ready(function($) {
    $('#_9m2pju-litesoc-test-connection').on('click', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var $status = $('#_9m2pju-litesoc-test-status');
        var apiKey = $('#litesoc_9m2pju_api_key').val();
        
        if (!apiKey) {
            $status.removeClass('success').addClass('error').text('Please enter an API Key first.').fadeIn();
            return;
        }
        
        $btn.prop('disabled', true).text(litesoc_9m2pju_vars.labels.testing);
        $status.hide().removeClass('success error');
        
        $.ajax({
            url: litesoc_9m2pju_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'litesoc_9m2pju_test_api',
                api_key: apiKey,
                nonce: litesoc_9m2pju_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    $status.removeClass('error').addClass('success').text(litesoc_9m2pju_vars.labels.success).fadeIn();
                } else {
                    $status.removeClass('success').addClass('error').text(litesoc_9m2pju_vars.labels.error + response.data.message).fadeIn();
                }
            },
            error: function() {
                $status.removeClass('success').addClass('error').text('AJAX error. Please try again.').fadeIn();
            },
            complete: function() {
                $btn.prop('disabled', false).text('Test Connection');
            }
        });
    });
});
