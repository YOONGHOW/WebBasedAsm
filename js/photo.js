
// ============================================================================
// Page Load (jQuery)
// ============================================================================

$(() => {

    // Photo preview
    $('label.upload input[type=file]').on('change', e => {
        const f = e.target.files[0];
        const img = $(e.target).siblings('img')[0];

        if (!img) return;

        img.dataset.src ??= img.src;

        if (f?.type.startsWith('image/')) {
            img.src = URL.createObjectURL(f);
        }
        else {
            img.src = img.dataset.src;
            e.target.value = '';
        }
    });



    // Drag and drop functionality
    $('label.upload').on('dragenter dragover', e => {
        e.preventDefault();  // Prevent default behavior, which is open the file in browser
        e.stopPropagation();
        $('#drag').css('border', '2px dotted #9b59b6');
    });

    $('label.upload').on('dragleave', e => {
        e.preventDefault();
        e.stopPropagation();
        $('#drag').css('border', '1px solid black');
    });

    $('label.upload').on('drop', e => {
        e.preventDefault();
        e.stopPropagation();

        const dt = e.originalEvent.dataTransfer;
        const f = dt.files[0];
        const img = $(e.currentTarget).find('img')[0]; // Use currentTarget to ensure we reference the label element
        const input = $(e.currentTarget).find('input[type=file]')[0];

        if (!img) return;

        img.dataset.src ??= img.src;

        if (f?.type.startsWith('image/')) {
            img.src = URL.createObjectURL(f);
            input.files = dt.files; //set the file to input[type=file]
        } else {
            img.src = img.dataset.src;
        }
    });

});

