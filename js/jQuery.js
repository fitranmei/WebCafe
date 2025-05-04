$(document).ready(function() {
    // Fungsi pencarian menu
    $('#menu-search').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();  
        var found = false;
        
        $('#menu-items .menu-item').each(function() {
            var itemName = $(this).data('name');  

            if (searchTerm === "" || itemName.indexOf(searchTerm) > -1) {
                $(this).show();  
                found = true;
            } else {
                $(this).hide();  
            }
        });
    });
});
