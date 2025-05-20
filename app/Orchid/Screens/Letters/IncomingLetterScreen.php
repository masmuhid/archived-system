<?php

namespace App\Orchid\Screens\Letters;

use App\Constants\GlobalMessages;
use App\Models\Letters\IncomingLetter;
use App\Models\User;
use App\Services\TelegramService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Upload;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Toast;

class IncomingLetterScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'incoming_letters' => IncomingLetter::filters()->defaultSort('incoming_date', 'desc')->paginate(10)
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Incoming Letters';
    }

    public function description(): ?string
    {
        return 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Add new')
                ->modal('myModal')
                ->method('createOrUpdate')
                ->icon('plus-square'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        $query = $this->query()['incoming_letters'];
        $number = ($query->currentPage() - 1) * $query->perPage() + 1;

        return [
            Layout::table('incoming_letters', [
                TD::make(__('#'))->render(function () use (&$number) {
                    return $number++;
                }),

                TD::make('reference_number',__('Reference Number'))
                    ->render(function (IncomingLetter $letter){
                        return
                        Link::make($letter->reference_number)
                            ->route('platform.letter.incoming', $letter)
                            ->asyncParameters([
                                'transaction' => $letter->id
                            ]);
                    })
                    ->filter()
                    ->sort(),

                TD::make('incoming_date', __('Incoming Date'))
                    ->render(function (IncomingLetter $letter) {
                        return
                        Link::make(__(\Carbon\Carbon::parse($letter->incoming_date)->format('d M Y')))
                                    ->route('platform.letter.incoming', $letter)
                                    ->asyncParameters([
                                        'transaction' => $letter->id
                                    ]);
                    })
                    ->sort(),
                    // ->filter(DateRange::make('incoming_date')),

                // TD::make('incoming_date',__('Date'))
                //     ->sort()
                //     ->filter(),

                TD::make('sender',__('Sender'))
                    ->filter()
                    ->sort(),

                TD::make('note',__('Note'))
                    ->filter()
                    ->sort(),

                // TD::make('status',__('Status'))
                //     ->sort()
                //     ->width('100px')
                //     ->render(function (IncomingLetter $letter) {
                //         $status = $letter->status == 1 ? 'Active' : 'Inactive';
                //         $badgeVariant = $letter->status == 1 ? 'success' : 'danger';
                //         return "<span class='badge bg-$badgeVariant'>$status</span>";
                //         })
                //     ->filter(TD::FILTER_SELECT, [
                //             '1' => 'Active',
                //             '0' => 'Inactive',
                //         ]),

                TD::make('updated_at', __('Last Update'))
                    ->sort()
                    ->width('160px')
                    ->render(function (IncomingLetter $letter) {
                        return (new DateTime($letter->updated_at))->format('Y-m-d H:i:s');
                    }),

                TD::make('updated_by', __('Updated By'))
                    ->render(function (IncomingLetter $letter) {
                        $userId = $letter->updated_by ?: $letter->created_by;
                        return optional(User::find($userId))->name;
                    }),

                TD::make(__('Actions'))
                    ->align(TD::ALIGN_RIGHT)
                    ->width('100px')
                    ->render(function (IncomingLetter $letter) {
                        return DropDown::make()
                            ->icon('bs.three-dots-vertical')
                            ->list([
                                ModalToggle::make(__('Edit'))
                                    ->icon('bs.pencil')
                                    ->modal('myModal')
                                    ->method('createOrUpdate')
                                    ->modalTitle(__('Edit Data'))
                                    ->asyncParameters([
                                        'letter' => $letter->id
                                    ]),

                                Button::make(__('Delete'))
                                    ->icon('bs.trash3')
                                    ->confirm(__('Are you sure you want to delete this data?'))
                                    ->method('remove', [
                                        'id' => $letter->id
                                    ]),
                            ]);
                    }),     
            ]),

            Layout::modal('myModal', Layout::rows([
                Input::make('letter.id')
                    ->type('hidden'),

                Input::make('letter.reference_number')
                    ->title('Reference Number')
                    ->placeholder('Enter reference number')
                    ->autofocus()
                    ->required(),

    //                 DateTimer::make('open')
    // ->format('Y-m-d')
    // ->allowEmpty()
    // ->multiple();

                DateTimer::make('letter.incoming_date')
                    ->format('Y-m-d')
                    ->title('Incoming Date')
                    ->placeholder('Enter incoming date')
                    ->required(),

                Input::make('letter.sender')
                    ->title('Sender')
                    ->placeholder('Enter sender')
                    ->required(),

                Upload::make('letter.file')
                    ->title('Letter File'),

                Input::make('letter.status')
                    ->type('hidden')
                    ->value(1),

                // CheckBox::make('letter.status')
                //     ->title('Status')
                //     ->placeholder('Active')
                //     ->sendTrueOrFalse()
                //     ->value(1),


            ]))
                ->title('Incoming Letters')
                ->applyButton('Save')
                ->async('asyncGetLetter'),

        ];
    }

    public function createOrUpdate(Request $request)
    {
        $letterData = $request->input('letter');
        $letterData['references'] = strtoupper($letterData['reference_number']);
        $letterData['file'] = $letterData['file'][0];

        $existingData = IncomingLetter::where('reference_number', $letterData['references'])
            ->when($letterData['id'] ?? null, function ($query) use ($letterData) {
                $query->where('id', '!=', $letterData['id']);
            })
            ->first();
        
        if (!$existingData) {
            $letter = IncomingLetter::firstOrNew(['id' => $letterData['id'] ?? null]);
            $letter->fill($letterData);

            if ($letter->exists) {
                $letter->updated_by = Auth::id();
                $letter->updated_at = now();
            } else {
                $letter->created_by = Auth::id();
                $letter->created_at = now();
            }

            $letter->save();
            
            $message = $letter->wasRecentlyCreated ? GlobalMessages::DATA_SAVED : GlobalMessages::DATA_UPDATED;
            Toast::info(__($message));

            // TelegramService::sendMessage("📬 Surat masuk baru telah diinput oleh admin.");
            $message = "📬 <b>Surat Masuk Baru</b>\n";
            $message .= "📌 <b>Nomor Referensi:</b> {$letter->reference_number}\n";
            $message .= "📤 <b>Pengirim:</b> {$letter->sender}\n";
            // $message .= "📥 <b>Tujuan:</b> {$letter->recipient}\n";
            // $message .= "📝 <b>Perihal:</b> {$letter->subject}\n";
            $message .= "📅 <b>Tanggal Surat:</b> {$letter->incoming_date}";

            TelegramService::sendMessage($message);
            
        } else {
            Alert::error(__(GlobalMessages::DUPLICATE_CODE));
            return;
        }
    }

    public function remove(IncomingLetter $letter)
    {
        $letter->delete();
        Toast::warning(__(GlobalMessages::DATA_DELETED));
    }

    public function asyncGetLetter(IncomingLetter $letter): array
    {
        return [
            'letter' => $letter->toArray(),
        ];
    }
}
