<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanBarangResource\Pages;
use App\Models\PeminjamanBarang;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\ViewAction; 
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Log;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\ImageUpload;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Card;
use App\Filament\Widgets\PeminjamanBarangStats;
use Illuminate\Http\Request;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PeminjamanBarangImport;
use App\Exports\PeminjamanBarangExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Forms\Components\CameraUpload;
use Illuminate\Support\Str;

class PeminjamanBarangResource extends Resource
{
    protected static ?string $model = PeminjamanBarang::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function getModelLabel(): string
    {
        return 'Peminjaman';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Peminjaman Barang';
    }

    public static function getWidgets(): array
    {
        return [
            PeminjamanBarangStats::class,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\View::make('filament.info')
                            ->extraAttributes(['class' => 'w-full col-span-full bg-sky-600']),
                    ]),
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('nama_siswa')
                        ->label('Nama Siswa')
                        ->placeholder('Masukan Nama Siswa')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('nama_siswa', strtoupper($state))),                    
                            
                        Forms\Components\TextInput::make('kelas')
                            ->label('Kelas')
                            ->placeholder('Masukan Kelas Anda')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'Proses' => 'Diproses',
                                'Done' => 'Selesai',
                            ])
                            ->default('Proses')
                            ->required(),
                           
                            CameraUpload::make('image')
                            ->label('Foto Siswa')
                            ->required()
                            ->visible(fn ($livewire) => 
                                !($livewire instanceof \Filament\Resources\Pages\ViewRecord) && (
                                    $livewire instanceof \Filament\Resources\Pages\CreateRecord ||
                                    $livewire instanceof \Filament\Resources\Pages\EditRecord
                                )// dia cuma nampilin gambar di tampilan view saja 
                            )
                            ->afterStateUpdated(function ($state) {
                                if ($state) {
                                    // Ambil data Base64
                                    $imageData = $state;
                        
                                    $imageParts = explode(',', $imageData);
                                    $imageType = explode(';', explode(':', $imageParts[0])[1])[0]; // Ambil jenis gambar (image/png)
                                    $imageData = base64_decode($imageParts[1]);
                        
                                    // buat nama file
                                    $fileName = 'foto_siswa_' . Str::random(10) . '.' . explode('/', $imageType)[1];
                        
                                    // Menyimpan di bagian folder images
                                    $folderPath = storage_path('app/public/images'); // folder storage/app/public/images
                                    if (!is_dir($folderPath)) {
                                        mkdir($folderPath, 0777, true);  // Buat folder images jika belum ada
                                    }
                        
                                    // Tentukan path lengkap untuk file gambar
                                    $filePath = $folderPath . '/' . $fileName;
                        
                                    // Simpan gambar ke file
                                    file_put_contents($filePath, $imageData);
                        
                                    // Kembalikan path file untuk disimpan dalam database
                                    return 'storage/images/' . $fileName;  
                                }
                        
                                return null;
                            }),
                        
                           
                        
                        
                        
                        Forms\Components\TextInput::make('nama_barang')
                            ->label('Nama Barang')
                            ->placeholder('Masukan Nama Barang')
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah Barang')
                            ->numeric()
                            ->placeholder('Jumlah Barang nya')
                            ->required(),
                        Forms\Components\TextInput::make('guru_mapel')
                            ->label('Guru Mapel')
                            ->placeholder('Masukan Guru Mapel Anda')
                            ->required()     
                            ->afterStateUpdated(fn ($state, callable $set)=>$set('guru_mapel',strtoupper($state))),
                        Forms\Components\TextInput::make('ruangan')
                            ->label('Ruang Kelas')
                            ->placeholder('Ruang Kelas Anda')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi / Keterangan')
                            ->placeholder('Keterangan Minjam')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\Action::make('importPeminjamanBarang')
                    ->label('Impor Data Peminjaman Barang')
                    ->requiresConfirmation()
                    ->modalHeading('Impor Data Peminjaman Barang')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('Pilih File Excel')
                            ->required()
                            ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->directory('uploads')
                            ->preserveFilenames(),
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
                            Excel::import(new PeminjamanBarangImport, $filePath);

                            Notification::make()
                                ->title('Sukses')
                                ->body('Data Peminjaman Barang berhasil diimpor!')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Gagal mengimpor data: ' . $e->getMessage());
                            Notification::make()
                                ->title('Gagal')
                                ->body('Gagal mengimpor data peminjaman barang: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('export')
                    ->label('Ekspor')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Akhir')
                            ->required(),
                    ])
                    ->action(function (Request $request) {
                        $startDate = $request->get('start_date');
                        $endDate = $request->get('end_date');
                        return Excel::download(new PeminjamanBarangExport($startDate, $endDate), 'peminjaman_barang_' . now()->format('Y-m-d') . '.xlsx');
                    }),
            ])
            ->columns([
                ImageColumn::make('image')->label('Foto Siswa')->size(120)->circular()->disk('public')->searchable(),
                TextColumn::make('nama_siswa')->label('Nama Siswa'),
                TextColumn::make('kelas')->label('Kelas'),
                TextColumn::make('nama_barang')->label('Nama Barang'),
                TextColumn::make('quantity')->label('Jumlah Barang'),
                TextColumn::make('guru_mapel')->label('Guru Mapel'),
                TextColumn::make('ruangan')->label('Ruang Kelas'),
                TextColumn::make('created_at')->label('Tanggal Peminjaman')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('Tanggal Peminjaman')
                    ->form([
                        Forms\Components\DatePicker::make('created_at')->label('Pilih Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when(
                            $data['created_at'],
                            fn (Builder $query, $date) => $query->whereDate('created_at', $date),
                        );
                    }),
            ])
            ->actions([
                Action::make('export_pdf')
                ->label('Ekspor PDF')
                ->action(function ($record) {
                  
                    $pdf = Pdf::loadView('pdf.peminjaman_barang', [
                        'record' => $record,
                    ]);
            
                  
                    $fileName = 'peminjaman_barang_' . $record->id . '.pdf';
            
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, $fileName);
                })
                ->color('primary')
                ->requiresConfirmation()
                ->tooltip('Ekspor data ini ke PDF'),
            
            

                ViewAction::make()
                ->modalContent(fn ($record) => view('filament.peminjaman-barang.view-modal', ['record' => $record])),
                Tables\Actions\EditAction::make(),
                DeleteAction::make()->label('Hapus')->requiresConfirmation(),
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
            'index' => Pages\ListPeminjamanBarangs::route('/'),
            'create' => Pages\CreatePeminjamanBarang::route('/create'),
            'edit' => Pages\EditPeminjamanBarang::route('/{record}/edit'),
        ];
    }
}