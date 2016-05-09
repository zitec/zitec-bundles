/**
 * Script for enabling the zitec autocomplete fields in Sonata admin interfaces.
 */
(function () {

    var sharedSetupBase = Admin.shared_setup;

    // Override the shared_setup method.
    Admin.shared_setup = function () {
        $('[data-zitec-autocomplete]').each(function () {
            new Zitec.Autocomplete($(this));
        });

        sharedSetupBase.apply(this, arguments);
    };

})();
