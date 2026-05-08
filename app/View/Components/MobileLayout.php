<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class MobileLayout extends Component
{
    public function __construct(
        public string $title = 'ICH Pendidikan',
        public string $pageTitle = 'ICH Pendidikan',
    ) {}

    public function render(): View
    {
        return view('layouts.mobile');
    }
}
