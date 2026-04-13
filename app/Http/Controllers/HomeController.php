<?php

namespace App\Http\Controllers;

use App\Services\ToolConfig;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    public function index()
    {
        $locale = App::getLocale();
        $tools = ToolConfig::allEnabled($locale);

        return view('home', [
            'tools' => $tools,
            'pageTitle' => __('home.meta_title'),
            'metaDescription' => __('home.meta_description'),
            'slug' => '',
        ]);
    }
}
