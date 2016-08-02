(function () {

    var AutocompleteLoader;

    /**
     * Widget which encapsulates an element used for loading data from the server into autocomplete fields.
     *
     * @param {jQuery|string} element
     *
     * @constructor
     */
    AutocompleteLoader = function (element) {
        /**
         * The widget's representing element.
         *
         * @type {jQuery}
         */
        this.element = $(element);

        /**
         * The data used for configuring the widget. It will be specified with data attributes.
         * Mandatory properties:
         *      - autocompleteLoaderSource: the internal id of the data loader. It will be used to identify
         *        the target fields;
         *      - autocompleteLoaderUrl: the URL for fetching the data to be loaded;
         *      - autocompleteLoaderParam: the name of parameter which will represent the value of the
         *        widget on the server-side;
         *      - autocompleteLoaderButtonLabel: the label of the load button;
         *      - autocompleteLoaderErrorMessage: the text displayed when an error occurs during loading;
         *
         * @type {*|jQuery}
         */
        this.data = $(element).data();

        /**
         * The load button.
         *
         * @type {jQuery}
         */
        this.button = null;

        this.init();
    };

    /**
     * Initialises the widget.
     *
     * @private
     */
    AutocompleteLoader.prototype.init = function () {
        var self = this;

        this.createButton();

        this.element.on('change', function () {
            self.setButtonState();
        });
        this.setButtonState();

        this.button.on('click', function (event) {
            event.preventDefault();

            self.loadData();
        });
    };

    /**
     * Creates the data loading button.
     *
     * @private
     */
    AutocompleteLoader.prototype.createButton = function () {
        this.button = $('<a></a>')
            .addClass('autocomplete-loader-add-button btn btn-small btn-success')
            .text(this.data.autocompleteLoaderButtonLabel);
        $('<i></i>')
            .addClass('glyphicon glyphicon-plus')
            .prependTo(this.button);

        // Place the button after the element.
        this.element.after(this.button);
    };

    /**
     * Toggles the loading button state: when the widget doesn't have a value, the button becomes disabled.
     *
     * @private
     */
    AutocompleteLoader.prototype.setButtonState = function () {
        if (!this.element.val()) {
            this.button.attr('disabled', 'disabled');
        } else {
            this.button.removeAttr('disabled');
        }
    };

    /**
     * Loads the data into the autocomplete.
     *
     * @private
     */
    AutocompleteLoader.prototype.loadData = function () {
        var requestData = {}, self = this;

        requestData[this.data.autocompleteLoaderParam] = this.element.val();

        $.ajax({
            url: this.data.autocompleteLoaderUrl,
            data: requestData
        }).success(function (data) {
            self.setData(data);
        }).fail(function () {
            self.showError();
        });
    };

    /**
     * Sets the data in the autocomplete target fields.
     *
     * @param {Object} data
     *
     * @private
     */
    AutocompleteLoader.prototype.setData = function (data) {
        var loaderId = this.data.autocompleteLoaderSource;

        $('[data-autocomplete-loader-target="' + loaderId + '"]').each(function () {
            $(this).trigger('zitec-autocomplete-force-data', [data, true]);
        });
    };

    /**
     * Handles the displaying of the error message.
     *
     * @private
     */
    AutocompleteLoader.prototype.showError = function () {
        var errorElement = $('<p></p>')
            .addClass('text-danger')
            .text(this.data.autocompleteLoaderErrorMessage);

        this.element.after(errorElement);
        setTimeout(function () {
            errorElement.fadeOut(500, function () {
                errorElement.remove();
            });
        }, 5000);
    };

    // Enable the widget on all of the associated elements.
    $(document).ready(function () {
        $('[data-autocomplete-loader-source]').each(function () {
            new AutocompleteLoader($(this));
        });
    });

})();
