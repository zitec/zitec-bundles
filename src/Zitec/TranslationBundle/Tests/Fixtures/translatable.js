(function () {
    var Translator, translatorService;

    Translator = function (translations) {
        this.translations = translations;
    };

    Translator.prototype.trans = function (id) {
        return this.translations[id];
    };
    
    Translator.prototype.transChoice = function (id, count) {
        return this.translations[id];
    };

    translatorService = new Translator({});
    translatorService.trans('test_js');
    translatorService.transChoice('{1} test_js |]1,Inf[ test_js %count%', 5);
})();
