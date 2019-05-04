<?php

Route::get('/{resource}/attachable', '\NovaAttachMany\Http\Controllers\AttachController@create');
Route::get('/{resource}/{resourceId}/attachable', '\NovaAttachMany\Http\Controllers\AttachController@edit');
