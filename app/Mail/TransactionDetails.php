<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionDetails extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $customerName;

    /**
     * Create a new message instance.
     *
     * @param  Order  $order
     * @param  string  $customerName
     * @return void
     */
    public function __construct(Order $order, string $customerName)
    {
        $this->order = $order;
        $this->customerName = $customerName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.transaction')
                    ->with([
                        'order' => $this->order,
                        'customerName' => $this->customerName,
                    ]);
    }
}