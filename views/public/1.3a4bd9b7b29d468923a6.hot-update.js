webpackHotUpdate(1,[
/* 0 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__components_translatable_input__ = __webpack_require__(11);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__components_translatable_input___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__components_translatable_input__);



var $ = window.$;

$(function () {
  new __WEBPACK_IMPORTED_MODULE_0__components_translatable_input___default.a({ localeInputSelector: '.js-locale-input' });

  // TinyMCE
  window.prestashop.component.initComponents(['TranslatableField', 'TinyMCEEditor', 'TranslatableInput']
  // 'EventEmitter',
  //'TextWithLengthCounter',
  );
});

/***/ })
])