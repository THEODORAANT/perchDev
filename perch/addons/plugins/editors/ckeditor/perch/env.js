// Default to the bundled CKEditor path to avoid CDN/network related editor load failures.
var CKEDITOR_BASEPATH = window.PerchCkeditorBasePath || (Perch.path + '/addons/plugins/editors/ckeditor/ckeditor-4/');
$('.ckeditor').removeClass('ckeditor').addClass('perch-ckeditor');
