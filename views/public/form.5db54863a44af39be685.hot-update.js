"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
self["webpackHotUpdateasblog"]("form",{

/***/ "./js/form/index.js":
/*!**************************!*\
  !*** ./js/form/index.js ***!
  \**************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _components_translatable_input__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @components/translatable-input */ \"../../../admin-dev/themes/new-theme/js/components/translatable-input.js\");\n/* harmony import */ var _components_form_choice_tree__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @components/form/choice-tree */ \"../../../admin-dev/themes/new-theme/js/components/form/choice-tree.js\");\n/* harmony import */ var _components_form_submit_button__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @components/form-submit-button */ \"../../../admin-dev/themes/new-theme/js/components/form-submit-button.js\");\n\n\n\n\n\nconst $ = window.$;\n\n$(() => {\n  let translatableInput = new _components_translatable_input__WEBPACK_IMPORTED_MODULE_0__[\"default\"]({localeInputSelector: '.js-locale-input'});\n\n  let selectedLocale = translatableInput.getSelectedLocale();\n  let titleInput =\n\n  // TinyMCE\n  window.prestashop.component.initComponents([\n    'TranslatableField',\n    'TinyMCEEditor',\n    'TranslatableInput',\n    'EventEmitter',\n    'TextWithLengthCounter',\n  ]);\n\n  $('.datetimepicker').datetimepicker({\n    locale: 'es',\n    useCurrent: false,\n    sideBySide: true\n  });\n\n  // Choice tree for category form\n  new _components_form_choice_tree__WEBPACK_IMPORTED_MODULE_1__[\"default\"]('#form_category_id_parent');\n  new _components_form_choice_tree__WEBPACK_IMPORTED_MODULE_1__[\"default\"]('#form_post_id_category');\n  new _components_form_choice_tree__WEBPACK_IMPORTED_MODULE_1__[\"default\"]('#form_category_shop_association').enableAutoCheckChildren();\n  new _components_form_submit_button__WEBPACK_IMPORTED_MODULE_2__[\"default\"]();\n\n\n});\n\n\n\n//# sourceURL=webpack://asblog/./js/form/index.js?");

/***/ })

},
/******/ function(__webpack_require__) { // webpackRuntimeModules
/******/ /* webpack/runtime/getFullHash */
/******/ (() => {
/******/ 	__webpack_require__.h = () => ("d2ebd07b093a1ea94b57")
/******/ })();
/******/ 
/******/ }
);