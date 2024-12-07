<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarisResource\Pages;
use App\Models\Inventaris;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InventarisImport;
use App\Exports\InventarisExport;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Actions\Tab;

class InventarisResource extends Resource
{
    protected static ?string $model = Inventaris::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    public function getTabs(): array
    {
        return [
            null => Tab::make('Semua Barang')
                ->query(fn ($query) => $query), // Menampilkan semua data tanpa filter
            'available' => Tab::make('Bisa Digunakan')
                ->query(fn ($query) => $query->where('kerusakan', 'Bisa-Digunakan')),
            'minor_damage' => Tab::make('Kerusakan Kecil')
                ->query(fn ($query) => $query->where('kerusakan', 'Kerusakan-Kecil')),
            'moderate_damage' => Tab::make('Kerusakan Sedang')
                ->query(fn ($query) => $query->where('kerusakan', 'Kerusakan-Sedang')),
            'severe_damage' => Tab::make('Kerusakan Besar')
                ->query(fn ($query) => $query->where('kerusakan', 'Kerusakan-Besar')),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        TextInput::make('kd_barang')
                            ->label('Kode Barang')
                            ->placeholder('Masukkan Kode Barang')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    // Cari data barang berdasarkan kode barang
                                    $barang = Inventaris::where('kd_barang', $state)->first();
                                    
                                    if ($barang) {
                                        // Set data field lainnya jika ditemukan
                                        $set('name', $barang->name);
                                        $set('quantity', $barang->quantity);
                                        $set('kerusakan', $barang->kerusakan);
                                        $set('description', $barang->description);
                                    } else {
                                        // Kosongkan field jika barang tidak ditemukan
                                        $set('name', null);
                                        $set('quantity', null);
                                        $set('kerusakan', null);
                                        $set('description', null);
                                    }
                                }
                            }),

                        TextInput::make('name')
                            ->label('Nama Barang')
                            ->placeholder('Nama Barang')
                            ->required(),

                        TextInput::make('quantity')
                            ->label('Jumlah Barang')
                            ->placeholder('Jumlah Barang')
                            ->numeric()
                            ->required(),

                        Select::make('kerusakan')
                            ->options([
                                'Bisa-Digunakan' => 'Bisa Digunakan',
                                'Kerusakan-Kecil' => 'Kerusakan Kecil',
                                'Kerusakan-Sedang' => 'Kerusakan Sedang',
                                'Kerusakan-Besar' => 'Kerusakan Besar',
                            ])
                            ->label('Kondisi Barang')
                            ->required(),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Deskripsi Kerusakan')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\Action::make('importInventaris')
                    ->label('Impor Data Inventaris')
                    ->requiresConfirmation()
                    ->modalHeading('Impor Data Inventaris')
                    ->form([
                        FileUpload::make('file')
                            ->label('Pilih File Excel')
                            ->required()
                            ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->directory('uploads') // Menyimpan di direktori uploads
                            ->preserveFilenames(), // Mempertahankan nama file
                    ])
                    ->action(function (array $data) {
                        if (empty($data['file'])) {
                            Notification::make()
                                ->title('Gagal')
                                ->body('File tidak ditemukan!')
                                ->danger()
                                ->send();
                            return;
                        }
                        
                        try {
                            $filePath = Storage::disk('public')->path('uploads/' . basename($data['file']));
                            Excel::import(new InventarisImport, $filePath);
                            
                            Notification::make()
                                ->title('Sukses')
                                ->body('Data inventaris berhasil diimpor!')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Gagal mengimpor data: ' . $e->getMessage());
                            Notification::make()
                                ->title('Gagal')
                                ->body('Gagal mengimpor data inventaris: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('export')
                    ->label('Ekspor')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('Tanggal Akhir')
                            ->required(),
                    ])
                    ->action(function ($data) {
                        return Excel::download(
                            new InventarisExport($data['start_date'], $data['end_date']),
                            'Inventaris_' . now()->format('Ymd') . '.xlsx'
                        );
                    }),
            ])
            ->columns([
                TextColumn::make('kd_barang')->label('Kode Barang')->searchable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('quantity'),
                TextColumn::make('kerusakan')
                ->label('Kondisi Barang')
                ->formatStateUsing(fn ($state) => $state)
                ->searchable()
                ->colors([
                    'success' => 'Bisa-Digunakan', // Green
                    'primary' => 'Kerusakan-Kecil', // Yellow
                    'warning' => 'Kerusakan-Sedang', // Orange
                    'danger' => 'Kerusakan-Besar', // Red
                ])
                ->icon(fn ($state) => match($state) {
                    'Bisa-Digunakan' => 'heroicon-o-check-circle',
                    'Kerusakan-Kecil' => 'heroicon-o-exclamation-circle',
                    'Kerusakan-Sedang' => 'heroicon-m-exclamation-triangle',
                    'Kerusakan-Besar' => 'heroicon-o-x-circle',
                }),
                TextColumn::make('description'),
            ])
            ->filters([
                Tables\Filters\Filter::make('Tanggal Peminjaman')
                    ->form([
                        DatePicker::make('created_at')->label('Pilih Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when(
                            $data['created_at'],
                            fn (Builder $query, $date) => $query->whereDate('created_at', $date),
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventaris::route('/'),
            'create' => Pages\CreateInventaris::route('/create'),
            'edit' => Pages\EditInventaris::route('/{record}/edit'),
        ];
    }
}
