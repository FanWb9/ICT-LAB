<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CameraUpload extends Field
{
    protected string $view = 'filament.camera-upload';
}
