<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class EmailTest extends Command
{
    /*
     *
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test';

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

        // Value initialization
        $day = 0;
        $count = 01;
        $amount = 0;
        $newDateString = '';
        $xnewDateString = '';


        do {
            $nature = $this->ask("\nChoose Transaction\n[1] Send List of Unclaimed Transactions to Field Offices\n[2] Send List of Transaction for Cancellation to Field Offices\n[3] Send Payrolls to Eight Under Par (Under Development)\n(1 - 3)");
        
            if ($nature == 1) {
                $final = $this->ask("\nYou are about to send the LIST OF UNCLAIMED TRANSACTIONS TO FIELD OFFICES.\nMake sure the files are already in the designated folders.\nPlease type CONFIRM to continue. Press ENTER to start over.");
                if (strtolower($final) !== 'confirm') {
                    continue;
                }
            } elseif ($nature == 2) {
                do {
                    $day = $this->ask("\nFinal claim is in how many days?\n(1 - 6)");
                    if ($day < 1 || $day > 6) {
                        $this->line('Invalid input. Choose from 1-6.');
                    }
                } while ($day < 1 || $day > 6);
                $newDateString = date('m/d/Y', strtotime('+' . $day . ' days'));
                $final = $this->ask("\nYou are about to send the LIST OF TRANSACTIONS FOR CANCELLATION ON $newDateString to Field Offices.\nMake sure the details are correct and the files are already in the designated folders.\nPlease type CONFIRM to continue. Press ENTER to start over.");
                if (strtolower($final) !== 'confirm') {
                    continue;
                }
            } elseif ($nature == 3) {
                do {
                    $amount = $this->ask("\nAmount Deposited?");
                    if (!is_numeric($amount)) {
                        $this->line('Invalid Amount. Try Again');
                    }
                } while (!is_numeric($amount));
        
                do {
                    $day = $this->ask("\nDeposited how many days ago?\n(1 - 6), 0 if deposited on the same day.");
                    if ($day < 0 || $day > 6) {
                        $this->line('Invalid input. Choose from 0-6.');
                    }
                } while ($day < 0 || $day > 6);
        
                $newDateString = date('m/d/Y', strtotime("-$day days"));
        
                do {
                    $program = $this->ask("\nProgram\n[1] GIP\n[2] TUPAD\n[3] BOTH\n(1 - 3)");
                    if ($program < 1 || $program > 3) {
                        $this->line('Invalid input. Choose from 1-3.');
                    }
                } while ($program < 1 || $program > 3);
        
                switch ($program) {
                    case '1':
                        $program = 'GIP';
                        break;
                    case '2':
                        $program = 'TUPAD';
                        break;
                    case '3':
                        $program = 'TUPAD AND GIP';
                        break;
                    default:
                        break;
                }
        
                do {
                    $final = $this->ask("\nAmount: " . number_format($amount, 2) . "\nDate Deposited: $newDateString\nProgram: $program\nIs this correct?\n[1] Yes\n[2] Input Again\n[3] Start Over\n(1-3)");
        
                    if ($final == 2) {
                        continue; // Input Again
                    } elseif ($final == 3) {
                        break 2; // Start Over
                    }
                } while ($final != 1);
            } elseif ($nature == 0 || $nature > 3) {
                $this->line('Invalid input. Please try again.');
            }
        } while ($nature == 0 || $nature > 3);
       

        if ($nature == 1 || $nature == 2){
            // Specify the root folder path within the storage directory
            $rootFolderPath = storage_path('app/Email');
            // Get all folders in the root folder
            $folders = File::directories($rootFolderPath);
            
            foreach ($folders as $folder) {
                // Get the folder name
                $folderName = basename($folder);
                $subFolders = File::directories($folder);
                // Sub Loop           
                foreach ($subFolders as $subFolder){

                    $files = File::allFiles($subFolder);
                    $subFolderName = basename($subFolder);

                    // Email and File Password Template
                    $destinationEmail = $this->getEmailForFolder($folderName, $subFolderName);
                    $password = $subFolderName[0] . 'dole11' . strtolower($folderName) . date('mdy');
                    
                    // Subject Picker
                    switch ($nature) {
                        case '1':
                            $subject = $folderName . ' ' . $subFolderName . ' - Unclaimed Palawan Transaction/s as of ' . date('m/d/Y');  // Replace with your desired subject
                            break;
                        case '2':
                            $subject = $folderName . ' ' . $subFolderName . ' - Palawan Transaction/s for CANCELLATION as of ' . date('m/d/Y');  // Replace with your desired subject
                            break;
                        default:
                            # code...
                            break;
                    }

                    // Send Mail, Skip if Empty
                    if (empty($files)) {
                        $this->info(sprintf("%02d", $count) . '. ' . $folderName . '-' . $subFolderName . ' has no Transactions. Skipping sending email.');
                        \Log::info($count);
                    }
                    else {$this->info(sprintf("%02d", $count) . '. Sending Transactions to ' . $folderName . '-' . $subFolderName . '... (' . $destinationEmail . ')');
                       $this->sendEmail($destinationEmail, $files, $password, $nature, $subject, $newDateString, $subFolderName, $xnewDateString);
                    }

                // Delete files after sending; Increment Log Count
                    File::delete($files);
                    $count = $count + 1;
                }
            }
        }
        else {
            $palawan = storage_path('app/Palawan');
            $files = File::allFiles($palawan);
            $oAmount = $amount;
            $destinationEmail = 'batchremsupport@palawanpawnshop.com';
            $subject = 'DOLE XI - DEPOSITED P' . number_format($oAmount,2) . ' ON ' . $newDateString . ' FOR ' . $program . ' BENEFICIARIES';
            $inwords = $this->convertNumberToWords($amount) . ' PESOS';
            $amount = substr(round(($amount-floor($amount)),2),2);
            \Log::info($subject);

            if ($amount == 1) {
                $inwords .= ' AND ' . $this->convertNumberToWords($amount) . ' CENTAVO ONLY';
            }
            elseif ($amount > 1) {
                $inwords .= ' AND ' . $this->convertNumberToWords($amount) . ' CENTAVOS ONLY';
            }
            else {
                $inwords .= ' ONLY';
            }

            $mailData = [
                'subject' => $subject,
                'newDateString' => $newDateString,
                'oAmount' => $oAmount,
                'inwords' => $inwords,
            ];

            // Send Mail, Skip if Empty
            if (empty($files)) {
                $this->info('No payrolls found.');
            }
                else {$this->info('Sending Payrolls to Eight Under Par, Inc... (' . $destinationEmail . ')');
            }
            
            Mail::send('palawan', $mailData, function ($message) use ($destinationEmail, $subject, $files) {
                $message->to($destinationEmail)->subject($subject);
    
                foreach ($files as $file) {
                    $message->attach($file);
                }
            });
            File::delete($files);
    
        }
        echo "Transactions emailed successfully.";
        echo "Press Enter to continue...";
        readline();
    }    

    /**
     * Get the email address based on the folder name.
     *
     * @param string
     * @return string
     */
    function getEmailForFolder($folderName, $subFolderName)
    {
        switch ($folderName) 
        {
            case 'DORFO':
                switch ($subFolderName)
                    {
                        case 'GIP':
                        case 'SPES':
                            return strtolower($folderName . $subFolderName . 'remittancedole11@gmail.com');
                        default:
                            return strtolower($subFolderName . 'remittance' . $folderName . 'dole11@gmail.com');
                    }
            case 'DOCFO':
                return 'remittancedocfodole11@gmail.com';
            case 'DCFO':
                switch ($subFolderName)
                    {
                        case 'GIP':
                        case 'SPES':
                        case 'TUPAD':
                            return strtolower($subFolderName . 'remit' . $folderName . 'dole11@gmail.com');
                    }
            case 'DSFO':
                switch ($subFolderName)
                    {
                        case 'GIP':
                        case 'SPES':
                            return strtolower($subFolderName . 'remit' . $folderName . 'dole11@gmail.com');
                        default:
                            return strtolower($subFolderName . 'remittance' . $folderName . 'dole11@gmail.com');
                    }                    
            case 'DNFO':
                switch ($subFolderName)
                    {
                        case 'TUPAD':
                            return strtolower($subFolderName . 'remittancedole11' . $folderName . '@gmail.com');
                        default:
                            return strtolower($subFolderName . 'remittance' . $folderName . 'dole11@gmail.com');
                    }
            case 'DIEO':
                switch ($subFolderName)
                    {
                        case 'GIP':
                            return strtolower($subFolderName . 'remit' . $folderName . 'dole11@gmail.com');
                        default:
                            return strtolower($subFolderName . 'remittance' . $folderName . 'dole11@gmail.com');
                    }
            case 'ROXI':
                switch ($subFolderName)
                    {
                        case 'SPES':
                        case 'GIP':
                            return 'employmentremittancerodole11@gmail.com';
                        default:
                            return strtolower($subFolderName . 'remittance' . $folderName . 'dole11@gmail.com');
                    }
                    
            default:
            return strtolower($subFolderName . 'remittance' . $folderName . 'dole11@gmail.com');
        }
    }

    /*
     * Send an email.
     *
     * @param string $destinationEmail
     * @param string $subject
     * @param string $content
     */
    function sendEmail($destinationEmail, $files, $password, $nature, $subject, $newDateString, $subFolderName, $xnewDateString)
    {
        $mailData = [
            'subject' => $subject,
            'nature' => $nature,
            'password' => $password,
            'xnewDateString' => $xnewDateString,
            'newDateString' => $newDateString,
        ];

        Mail::send('email', $mailData, function ($message) use ($destinationEmail, $subject, $files, $subFolderName) {
            $message->to($destinationEmail)->subject($subject);

            switch ($subFolderName) {
                case 'GIP':
                case 'SPES':
                    if ($destinationEmail == 'employmentremittancerodole11@gmail.com') {
                        break;
                    }
                    else {
                        $message->cc('employmentremittancerodole11@gmail.com');
                    }
                    break;
                default:
                break;
            }

            foreach ($files as $file) {
                // Attach each file to the email
                $message->attach($file);
            }
        });
    }
    
    function convertNumberToWords($amount) 
    {
        $words = [
            0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen',
            20 => 'twenty', 30 => 'thirty', 40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
        ];
    
        if ($amount < 20) {
            return $words[$amount];
        }
    
        if ($amount < 100) {
            return $words[(($amount / 10) * 10)-($amount % 10)] . '-' . $this->convertNumberToWords($amount % 10);
        }
    
        if ($amount < 1000) {
            return $words[$amount / 100] . ' hundred ' . $this->convertNumberToWords($amount % 100);
        }
    
        if ($amount < 1000000) {
            return $this->convertNumberToWords($amount / 1000) . ' thousand ' . $this->convertNumberToWords($amount % 1000);
        }

        if ($amount < 1000000000) {
            return $this->convertNumberToWords($amount / 1000000) . ' million ' . $this->convertNumberToWords($amount % 1000000);
        }    

        return 'Number out of range';
    }

}
