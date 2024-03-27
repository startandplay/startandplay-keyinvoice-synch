(function( $ ) {
	'use strict';
	$( window ).load(function() {
        $("#test-api-ki").click(function(){
            $("#result-testing").html('');
            var btn = $(this);
            btn.prop('disabled', true);
            $.ajax({
                url: ajaxurl,
                data: {
                    action: "test_api",
                    api_url: $("input[name='url_api']").val(),
                    api_key: $("input[name='api_key']").val()
                },
                dataType: "json",
                method: "POST",
                success: function (response) {
                    if (response) 
                    {
                        $('#result-testing').html(response.msg);
                    }
                },
                error: function (errorThrown) {
                  console.log(errorThrown);
                },
                complete: function() {
                    btn.prop('disabled', false);
                }
            });
        });
        $("#synch-products-ki").click(function(){
            $("#result-synch").html('');
            var btn = $(this);
            btn.prop('disabled', true);
            $.ajax({
                url: ajaxurl,
                data: {
                    action: "synch_products",
                },
                dataType: "json",
                method: "POST",
                success: function (response) {
                    if (response) 
                    {
                        $('#result-synch').html(response.msg);
                    }
                },
                error: function (errorThrown) {
                  console.log(errorThrown);
                },
                complete: function()
                {
                    btn.prop('disabled', false);
                }
            });
        });
	});
})( jQuery );

