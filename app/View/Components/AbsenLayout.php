<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AbsenLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        // Ini akan mengarahkan <x-absen-layout> ke file resources/views/layouts/absen.blade.php
        return view('layouts.absen');
    }
}
