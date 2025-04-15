import $ from 'jquery';

$(document).ready(function () {
    $('.select2-ajax').each(function () {
        let $this = $(this);
        let url = $this.data('url');
        let placeholder = $this.data('placeholder') || 'Select option';

        if (!url) {
            console.warn('No data-url found for Select2 element:', $this);
            return;
        }

        alert(url);
    });
});
