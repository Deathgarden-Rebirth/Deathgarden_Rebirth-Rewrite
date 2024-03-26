<?php

namespace App\View\Components\Inputs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

class AssetDropdown extends Component
{
    public array $options = [];

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $folderPath,
        public string $selected)
    {
        $files = Storage::disk('public')->files($this->folderPath);

        foreach ($files as $file) {
            $this->options[] = pathinfo($file, PATHINFO_FILENAME);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.inputs.asset-dropdown');
    }
}
