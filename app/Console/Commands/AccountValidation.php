<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class AccountValidation extends Command
{
    /*
     *
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:check';

    /*
     *
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /*
     *
     * Execute the console command.
     */
    public function handle()
    {
        #Change $bank_account_number to match cds data
        #validation returns TRUE or FALSE

        $str = "432765432";
        $bank_account_number = $this->ask("Account number:");//to remove
        $checker = intval(substr($bank_account_number, 3, 1)); // toremove
        \Log::info($checker);

    if (strlen($bank_account_number) != 10) {
        $this->errorMsg = "Account number should be ten digits.";
        return false;
    }

    if (!is_numeric($bank_account_number)) {
        $this->errorMsg = "Account number not numeric.";
        return false;
    }

    if (($checker == 0 || $checker == 8 || $checker == 9)) {
        $this->info("invalid"); //console command only, to remove
        $this->errorMsg = $bank_account_number . " is an invalid account number. Please check the bank details again.";
        return false;
    }
    

    $num1 = 0;

    for ($startIndex = 0; $startIndex <= 8; $startIndex++) {
        $num1 += intval(substr($bank_account_number, $startIndex, 1)) * intval(substr($str, $startIndex , 1));
        \Log::info(substr($bank_account_number, $startIndex, 1)); //remove log
        \Log::info(substr($str, $startIndex, 1)); // remove log
    }
    \Log::info($num1); // remove log
    $num2 = $num1 % 11;
    \Log::info($num2); // remove log
    if ((($num2 != 0 && $num2 != 1) ? (11 - $num2) : 0) != intval(substr($bank_account_number, 9, 1))) {
        $this->errorMsg = $bank_account_number . " is an invalid account number. Please check the bank details again.";
        $this->info("invalid"); //console command only, to remove
        return false;
    }

    $this->info("valid"); //console command only, to remove
    return true;
}

}
