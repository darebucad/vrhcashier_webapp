<?php
Auth::routes();

Route::get('/', 'DashboardController@index')->name('dashboard');

// Collections Outpatient
Route::get('collections/outpatient', 'CollectionsOutpatientController@index')->name('collections.outpatient');
Route::get('collections/outpatient/create/{id}', 'CollectionsOutpatientController@create')->name('collections.outpatient.create');
Route::get('collections/outpatient/create/load_data', 'CollectionsOutpatientController@loadData');
Route::post('collections/outpatient/create/post_data', 'CollectionsOutpatientController@postData');
Route::post('collections/outpatient/create/postajax','CollectionsOutpatientController@post');
Route::get('collections/outpatient/create/get_or_number', 'CollectionsOutpatientController@getORNumber');
Route::post('collections/outpatient/create/payment', 'CollectionsOutpatientController@store');
Route::post('collections/outpatient/create/apply_discount_all', 'CollectionsOutpatientController@applyDiscountAll');
Route::post('collections/outpatient/create/apply_discount_selected', 'CollectionsOutpatientController@applyDiscountSelected');
Route::post('collections/outpatient/create/clear_discount', 'CollectionsOutpatientController@clearDiscount');
Route::get('/collections/outpatient/print', 'CollectionsOutpatientController@pdfIndex');
Route::get('/collections/outpatient/print/pdf/{id}', 'CollectionsOutpatientController@showPDF')->name('collections.outpatient.print.pdf'); // print OR pdf
Route::get('/collections/outpatient/cancel/payment', 'CollectionsOutpatientController@cancelPayment');
Route::get('/collections/outpatient/payment/edit', 'CollectionsOutpatientController@edit');
Route::post('collections/outpatient/create', 'CollectionsOutpatientController@show')->name('collections.outpatient.create.show');
Route::get('collections/outpatient/getdata', 'CollectionsOutpatientController@getdata')->name('collections.outpatient.getdata');
Route::get('/collections/outpatient/get_outpatient_payment_data', 'CollectionsOutpatientController@getOutpatientPaymentData');
Route::post('/collections/outpatient/update_total', 'CollectionsOutpatientController@updateTotal');

// Collections Other
Route::get('/collections/other', 'CollectionsOtherController@index')->name('collections.other');
Route::get('collections/other/create/{id}', 'CollectionsOtherController@create')->name('collections.other.create');
Route::get('/collections/other/show_products', 'CollectionsOtherController@showProducts')->name('collections.other.show_products');
Route::get('collections/other/get_latest_price', 'CollectionsOtherController@getLatestPrice')->name('collections.get_latest_price');
Route::post('collections/other/store_payment', 'CollectionsOtherController@storePayment')->name('collections.store_payment');
Route::get('collections/other/get_patient_list', 'CollectionsOtherController@getPatientList');
Route::get('/collections/other/print/pdf/{id}', 'CollectionsOtherController@printPDF'); // print other collection OR pdf
Route::get('/collections/other/autocomplete-search', 'CollectionsOtherController@search');
Route::get('/collections/outpatient/get-other-collection-data', 'CollectionsOtherController@getOtherCollectionData');


// Collections Inpatient
Route::get('collections/inpatient', 'CollectionsInpatientController@index')->name('collections.inpatient');
Route::get('collections/inpatient/create', 'CollectionsInpatientController@create');
Route::get('collections/inpatient/create/get-patient-bill', 'CollectionsInpatientController@getPatientBill');
Route::get('collections/inpatient/create/autocomplete-search', 'CollectionsInpatientController@autoCompleteSearch');


// Collections Walkin
Route::get('collections/walkin', 'CollectionsWalkinController@index');
Route::get('collections/walkin/create/{id}', 'CollectionsWalkinController@create')->name('collections.walkin.create');
Route::post('collections/walkin/create/search-walkin-charges', 'CollectionsWalkinController@searchWalkinCharges');
Route::post('collections/walkin/create/get-discount-percent', 'CollectionsWalkinController@getDiscountPercent');
Route::post('collections/walkin/create/apply-discount-all', 'CollectionsWalkinController@applyDiscountAll');
Route::post('collections/walkin/create/apply-discount-selected', 'CollectionsWalkinController@applyDiscountSelected');
Route::post('collections/walkin/create/clear-discount', 'CollectionsWalkinController@clearDiscount');
Route::post('collections/walkin/create/update-totals', 'CollectionsWalkinController@updateTotals');
Route::post('collections/walkin/create/save-walkin-charges', 'CollectionsWalkinController@saveWalkinCharges');
Route::get('collections/walkin/create/print-pdf/{id}', 'CollectionsWalkinController@printPdf');
Route::get('collections/walkin/get-walkin-payment-data', 'CollectionsWalkinController@getWalkinPaymentDataIndex');


//Settings User Account
Route::get('settings/user_account', 'UsersController@index')->name('settings.user_account');
Route::get('settings/user_account/create', 'UsersController@create');

// Settings Cashier Management
Route::get('settings/cashier-management', 'SettingsCashierController@index');

//Success
Route::get('/auth/success', ['as'   => 'auth.success','uses' => 'Auth\AuthController@success']);




// use JasperPHP\JasperPHP as JasperPHP;
//
// Route::get('/', function () {
//
//     $jasper = new JasperPHP;

	// Compile a JRXML to Jasper
    // $jasper->compile(__DIR__ . '/../../vendor/cossou/jasperphp/examples/hello_world.jrxml')->execute();

	// Process a Jasper file to PDF and RTF (you can use directly the .jrxml)
    // $jasper->process(
    //     __DIR__ . '/vendor/cossou/jasperphp/examples/hello_world.jasper',
    //     false,
    //     array("pdf", "rtf"),
    //     array("php_version" => "xxx")
    // )->execute();



	// List the parameters from a Jasper file.
    // $array = $jasper->list_parameters(
    //     __DIR__ . '/vendor/cossou/jasperphp/examples/hello_world.jasper'
    // )->execute();


//
//     return view('welcome');
// });
