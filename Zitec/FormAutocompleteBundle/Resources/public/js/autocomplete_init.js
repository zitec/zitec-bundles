/**
 * Script for enabling the zitec autocomplete fields in a page.
 */
(function () {

    $(document).ready(function () {
        $('[data-zitec-autocomplete]').each(function () {
            new Zitec.Autocomplete($(this));
        })
    });

})();
