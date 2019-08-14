<?php

Route::get('api/docs', function () {
    return view('shine-swagger::docs.index');
});