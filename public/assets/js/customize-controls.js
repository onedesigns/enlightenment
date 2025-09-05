/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/customize-controls.js":
/*!**************************************!*\
  !*** ./src/js/customize-controls.js ***!
  \**************************************/
/***/ (() => {

eval("(function (exports, $) {\n  \"use strict\";\n\n  var api = wp.customize;\n  api.bind('ready', function () {\n    api('custom_logo', function (value) {\n      api.control('custom_logo_alt', function (control) {\n        /**\n         * Toggling function\n         */\n        var toggle = function toggle(to) {\n          control.toggle(!!to);\n        };\n\n        // 1. On loading.\n        toggle(value.get());\n\n        // 2. On value change.\n        value.bind(toggle);\n      });\n    });\n    api('navbar_nav_overflow', function (value) {\n      value.bind(function (to) {\n        if ('wrap' == to) {\n          api('site_header_position', function (setting) {\n            setting.set('static-top');\n          });\n        }\n      });\n    });\n  });\n})(wp, jQuery);\n\n//# sourceURL=webpack://enlightenment/./src/js/customize-controls.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./src/js/customize-controls.js"]();
/******/ 	
/******/ })()
;