<?php

namespace App\Http\Controllers;

use App\Services\ToolConfig;

class HomeController extends Controller
{
    public function index()
    {
        $tools = ToolConfig::allEnabled();

        return view('home', [
            'tools' => $tools,
            'pageTitle' => 'Online PDF-Tools — Schnell & Sicher',
            'metaDescription' => 'sofortpdf.com — Ihre Online-PDF-Tools. PDF zusammenfügen, komprimieren, umwandeln und mehr. Schnell, sicher und ohne Installation.',
            'slug' => '',
        ]);
    }
}
