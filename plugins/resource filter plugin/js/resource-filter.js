jQuery(document).ready(function($) {
    $('#filter-form select[name="taxonomy_term"]').change(function() {
        var taxonomyTermId = $(this).find(':selected').data('id');

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'fetch_filtered_by_taxonomy',
                taxonomy_term_id: taxonomyTermId,
            },
            success: function(response) {
                if (response.success) {
                    $('#resource-list').html(response.data);
                } else {
                    $('#resource-list').html('<p>No resources found.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', status, error);
            }
        });
    });

    var debounceTimeout;
    $('#filter-form input[name="keyword"]').on('input', function() {
        clearTimeout(debounceTimeout);

        debounceTimeout = setTimeout(function() {
            var keyword = $('input[name="keyword"]').val();


            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'fetch_filtered_by_keyword',
                    keyword: keyword,

                },
                success: function(response) {
                    if (response.success) {
                        $('#resource-list').html(response.data);
                    } else {
                        $('#resource-list').html('<p>No resources found.</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:', status, error);
                }
            });
        }, 1500);
    });
});
