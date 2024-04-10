<?php

namespace App\Actions;

use App\Models\Invoice;

class InvoiceAction
{
    public function createInvoice($data)
    {
        $item = Invoice::create($data);
        return $item->id;
    }
}
