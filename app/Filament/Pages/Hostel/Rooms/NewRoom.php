<?php

namespace App\Filament\Pages\Hostel\Rooms;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\Room;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class NewRoom extends BaseHostelPage implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-plus-circle';

    protected static string | \UnitEnum | null $navigationGroup = 'Room Management';

    protected static ?string $title = 'Add / Edit Room';

    protected static ?string $navigationLabel = 'Add / Edit Room';

    protected static ?string $slug = 'hostel/rooms/new';

    protected static ?int $navigationSort = 22;

    protected string $view = 'filament.pages.hostel.rooms.new-room';

    public ?Room $record = null;

    public array $data = [];

    public function getFormProperty(): ?Schema
    {
        return $this->getSchema('form');
    }

    public function mount(): void
    {
        $recordId = request()->integer('record');

        $this->record = $recordId ? Room::query()->find($recordId) : null;

        if ($this->record) {
            $this->form->fill([
                ...$this->record->toArray(),
                'images' => $this->record->imagePaths(),
            ]);
        } else {
            $this->form->fill([
                'status' => 'available',
                'room_type' => 'ac',
                'floor' => 1,
                'capacity' => 1,
                'base_rate' => 0,
                'images' => [],
            ]);
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model($this->record ?? Room::class)
            ->statePath('data')
            ->columns(2)
            ->components([
                TextInput::make('room_number')
                    ->label('Room Number')
                    ->required()
                    ->maxLength(20)
                    ->rule(fn () => Rule::unique(Room::class, 'room_number')->ignore($this->record)),

                TextInput::make('floor')
                    ->label('Floor')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                Select::make('room_type')
                    ->label('Room Type')
                    ->required()
                    ->options([
                        'ac' => 'AC',
                        'non_ac' => 'Non AC',
                        'vip' => 'VIP',
                    ]),

                TextInput::make('capacity')
                    ->label('Capacity')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                TextInput::make('base_rate')
                    ->label('Base Rate (BDT)')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'available' => 'Available',
                        'occupied' => 'Occupied',
                        'maintenance' => 'Maintenance',
                    ]),

                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull()
                    ->rows(4),

                FileUpload::make('images')
                    ->label('Room Pictures')
                    ->disk('public')
                    ->directory('rooms')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->maxFiles(30)
                    ->maxSize(100)
                    ->minFiles(fn (): int => $this->record === null ? 1 : 0)
                    ->helperText('Maximum 100 KB per image. Multiple images allowed.')
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $validated = $this->form->getState();

        $validated['images'] = array_values(array_filter(
            $validated['images'] ?? [],
            static fn ($path): bool => is_string($path) && $path !== '',
        ));

        $room = $this->record
            ? tap($this->record)->update($validated)
            : Room::query()->create($validated);

        $this->record = $room->fresh();

        Notification::make()
            ->title('Room saved')
            ->success()
            ->send();

        $this->redirect(RoomInventory::getUrl(panel: 'admin'));
    }
}
