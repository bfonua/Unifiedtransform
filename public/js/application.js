window.addEventListener('load', function () {
    loader_fade_out()
    data_table_div()
    all_images()
    datepicker_format()
})

function loader_fade_out() {
    $('.loader').fadeOut();
}

function data_table_div() {
    // Check if DataTables is loaded before initializing
    if (typeof $.fn.DataTable !== 'undefined' && $('.table-data-div').length > 0) {
        var myTable = $('.table-data-div').DataTable({ paging: false });
    }
}

function all_images() {
    var allimages = document.getElementsByTagName('img');
    for (var i = 0; i < allimages.length; i++) {
        if (allimages[i].getAttribute('data-src')) {
            allimages[i].setAttribute('src', allimages[i].getAttribute('data-src'));
        }
    }
}

function datepicker_format() {
    $('.datepicker').datepicker({format: 'yyyy-mm-dd'});
}
