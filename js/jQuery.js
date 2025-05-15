$(document).ready(function() {
    $('#menu-search').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();  
        
        $('#menu-items .menu-item').each(function() {
            var itemName = $(this).data('name');  

            if (searchTerm === "" || itemName.indexOf(searchTerm) > -1) {
                $(this).show();  
            } else {
                $(this).hide();  
            }
        });
    });
});