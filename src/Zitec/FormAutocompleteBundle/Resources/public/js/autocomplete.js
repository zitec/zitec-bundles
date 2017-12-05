(function () {

    var Autocomplete;

    /**
     * Class which configures zitec_autocomplete form fields to use the Select2 plugin for enabling the autocomplete
     * functionality.
     *
     * @param {string|jQuery} element
     *
     * @constructor
     */
    Autocomplete = function (element) {
        /**
         * The form element.
         *
         * @type {jQuery}
         */
        this.element = $(element);

        /**
         * The data for configuring the element.
         *
         * @type {Object}
         */
        this.data = this.element.data('zitec-autocomplete');

        /**
         * Flag which determines if a version of Select2 lower than 4.0.0 is used.
         *
         * @type {boolean}
         */
        this.compatibility = !!this.data.compatibility;

        this.init();
    };

    /**
     * Builds the common options of the compatibility and default modes.
     *
     * @returns {Object}
     *
     * @private
     */
    Autocomplete.prototype.buildBaseOptions = function () {
        var options;

        options = {
            ajax: {
                url: this.data.url,
                dataType: 'json',
                cache: true
            },
            minimumInputLength: this.data.minimumInputLength,
            language: this.data.language
        };

        if (null !== this.data.placeholder) {
            options.placeholder = this.data.placeholder;
        }

        return options;
    };

    /**
     * Configures the autocomplete 'delay' option, which determines how much time will pass until the user will
     * see the suggestions after introducing the input.
     *
     * @param {Object} options
     *
     * @private
     */
    Autocomplete.prototype.configureDelay = function (options) {
        if (!this.compatibility) {
            options.ajax.delay = this.data.delay;
        } else {
            options.ajax.quietMillis = this.data.delay;
        }
    };

    /**
     * Configures the data function, which is responsible for building the request data.
     *
     * @param {Object} options
     *
     * @private
     */
    Autocomplete.prototype.configureDataFunction = function (options) {
        var self = this;

        options.ajax.data = function (params) {
            var requestData = {term: !self.compatibility ? params.term : params};

            // The user may also specify a context to send to the autocomplete path for fetching suggestions.
            if (null !== self.data.context) {
                requestData.context = self.data.context;
            }

            return requestData;
        };
    };

    /**
     * Configures the results processor function.
     *
     * @param {Object} options
     *
     * @private
     */
    Autocomplete.prototype.configureProcessResultsFunction = function (options) {
        var processResultsFnName;

        processResultsFnName = !this.compatibility ? 'processResults' : 'results';
        options.ajax[processResultsFnName] = function (data) {
            return {
                results: data.items
            };
        };
    };

    /**
     * Decides how the default values will be set on the field.
     *
     * @param {Object} options
     *
     * @private
     */
    Autocomplete.prototype.configureInitialSelection = function (options) {
        var self = this;

        // On the default mode, the default values will be taken directly from the DOM.
        if (!this.compatibility) {
            return;
        }

        // The compatibility mode demands a custom function for setting the default values.
        options.initSelection = function (element, callback) {
            var defaults = self.data.defaults, items, i, l;

            if (null === defaults) {
                return;
            }

            if ($.isArray(defaults)) {
                items = [];
                for (i = 0, l = defaults.length; i < l; i++) {
                    items.push({
                        id: defaults[i].value,
                        text: defaults[i].label
                    });
                }
            } else {
                items = {id: defaults.value, text: defaults.label};
            }

            callback(items);
        };
    };

    /**
     * Configures the 'multiple' option of the field.
     *
     * @param {Object} options
     *
     * @private
     */
    Autocomplete.prototype.configureMultipleElement = function (options) {
        // On the default mode, the multiple option will be directly specified in the DOM.
        if (this.compatibility) {
            options.multiple = this.data.multiple;
        }
    };

    /**
     * Configures the 'allowClear' option of the field.
     *
     * @param {Object} options
     *
     * @private
     */
    Autocomplete.prototype.configureAllowClear = function (options) {
        var self = this;

        options.allowClear = this.data.allowClear;
        if (options.allowClear && !this.compatibility) {
            this.element.on('select2:unselecting', function () {
                self.element.data('unselecting', true);
            }).on('select2:open', function () {
                if (self.element.data('unselecting')) {
                    self.element.removeData('unselecting');
                    self.element.select2('close');
                }
            });
        }
    };

    /**
     * Configures the 'dropdownParent' option of the field.
     *
     * @param {Object} options
     *
     * @private
     */
    Autocomplete.prototype.configureDropdownParent = function (options) {
        if (this.data.dropdownParent) {
            options.dropdownParent = $(this.data.dropdownParent);
        }
    };

    /**
     * Adds other options passed by the consumer.
     *
     * @param {Object} options
     *
     * @private
     */
    Autocomplete.prototype.addOtherOptions = function (options) {
        if (this.data.otherOptions) {
            $.extend(options, this.data.otherOptions);
        }
    };

    /**
     * Initialises the widget.
     *
     * @private
     */
    Autocomplete.prototype.init = function () {
        var options, self = this;

        options = this.buildBaseOptions();

        this.configureDelay(options);
        this.configureDataFunction(options);
        this.configureProcessResultsFunction(options);
        this.configureInitialSelection(options);
        this.configureMultipleElement(options);
        this.configureAllowClear(options);
        this.configureDropdownParent(options);

        this.addOtherOptions(options);

        this.element.select2(options);
        if (this.compatibility) {
            this.element.select2('val', []);
        }

        // An external widget may explicitly set data to the input by triggering this event.
        this.element.on('zitec-autocomplete-force-data', function (event, data, append) {
            var elementData = [];

            event.stopPropagation();

            if (append) {
                elementData = self.element.select2('data');
            }
            elementData = elementData.concat(data.data);

            self.element.select2('data', elementData);
        });
    };

    // Register the widget in the Zitec namespace.
    if (typeof Zitec === 'undefined') {
        Zitec = {};
    }
    Zitec.Autocomplete = Autocomplete;

})();
