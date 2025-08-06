<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class calculate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {   $this->info("This test assumes asumes the account no 2016-9032-59, nca of 0003392, acic no 24-04-038, and starting check no 0001962705"); //to remove
        $amount = $this->ask("amount");  //
        $account_number = "2016-9032-59";
        $nca_number = "000339-2";
        $acic_number = "24-04-038";
        $check_number = "1962705";
        
        $acct = str_replace("-", "", $account_number);
        $acicpad = str_pad(str_replace("-","",$acic_number),10, '0', STR_PAD_LEFT);
        $ncapad = str_pad(str_replace("-","",$nca_number),10,'0', STR_PAD_LEFT);
        $checkpad = str_pad($check_number,10,'0', STR_PAD_LEFT);
        $hash_total = 0;
        $hash = 0;

        $str="1523412453";
        $num1 = (substr($acct, 6 , 1));
        $num2 = (substr($acicpad, 5 , 1));
        $num3 = (substr($ncapad, 8 , 1));
        $num4 = (substr($checkpad, 7 , 1));

        //foreach( $check as $check_number) {
            for ($startIndex = 0; $startIndex <= 9; $startIndex++){
                $hash += (intval(substr($acct, $startIndex, 1)) + intval(substr($acicpad, $startIndex, 1)) + intval(substr($ncapad, $startIndex, 1)) + intval(substr($checkpad, $startIndex, 1))) * intval(substr($str, $startIndex, 1));
            }
            if ($num1 == 0){
                $num1 = 1;
            };
            if ($num2 == 0){
                $num2 = 1;
            };
            if ($num3 == 0){
                $num3 = 1;
            };
            if ($num4 == 0){
                $num4 = 1;
            };
            $hash_total += ($hash * $num1 * $num2 * $num3 * $num4 * $amount);
            $this->info("Hash total: " . $hash_total);
        }
        
    //}
}
