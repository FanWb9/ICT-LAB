<?php
namespace App\Filament\Pages;
use App\Filament\Widgets\TabelPeminjamanBarangWidget;
use App\Filament\Widgets\TabelInventarisWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    Public function getWidgets(): array
    {
        return [
            TabelPeminjamanBarangWidget::class,
            TabelInventarisWidget::class,
        ];
    }
}