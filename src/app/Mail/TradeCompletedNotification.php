<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Item;
use App\Models\User;

class TradeCompletedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $item;
    public $purchaser;
    public $seller;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Item $item, User $purchaser, User $seller)
    {
        $this->item = $item;
        $this->purchaser = $purchaser;
        $this->seller = $seller;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.trade-completed')
            ->subject('取引が完了しました')
            ->with([
                'item' => $this->item,
                'purchaser' => $this->purchaser,
                'seller' => $this->seller,
            ]);
    }
}
