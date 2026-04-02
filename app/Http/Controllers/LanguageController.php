<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function switch(Request $request, $locale)
    {
        if (in_array($locale, ['en', 'id'])) {
            $request->session()->put('locale', $locale);
            App::setLocale($locale);
        }

        return redirect()->back();
    }
}
