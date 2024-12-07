<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PeminjamanBarangResource; 
use App\Models\PeminjamanBarang;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker; 
use Illuminate\Database\Eloquent\Builder; 

class TabelPeminjamanBarangWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(PeminjamanBarang::query()) 
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nama_siswa')
                    ->label('Nama Siswa')
                    ->searchable(),
                  
                Tables\Columns\TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable(),
                   
                Tables\Columns\TextColumn::make('kelas')
                    ->label('Kelas')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Peminjaman')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tanggal Pengembalian')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state) => $state === 'Done' ? 'Selesai' : 'Diproses')
                    ->colors([
                        'success' => fn ($state) => $state === 'Done',
                        'warning' => fn ($state) => $state === 'Proses',
                    ])
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('tanggal_peminjaman')
                    ->label('Tanggal Peminjaman')
                    ->form([
                        DatePicker::make('start_date') // Gunakan DatePicker yang benar
                            ->label('Tanggal Mulai')
                            ->required(),
                        DatePicker::make('end_date') // Gunakan DatePicker yang benar
                            ->label('Tanggal Akhir')
                            ->required(),
                    ])
                    ->query(function (Builder $query, array $data) { 
                        return $query->when(
                            $data['start_date'],
                            fn (Builder $query, $startDate) => $query->whereDate('created_at', '>=', $startDate),
                        )->when(
                            $data['end_date'],
                            fn (Builder $query, $endDate) => $query->whereDate('created_at', '<=', $endDate),
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('edit') 
                    ->label('Buka')
                    ->url(fn (PeminjamanBarang $record): string => PeminjamanBarangResource::getUrl('edit', ['record' => $record]))
                    ->color('warning'),
            ]);
    }
}
