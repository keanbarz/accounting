<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class emailChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:check';

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
    {
        // Specify the root folder path within the storage directory
        $rootFolderPath = storage_path('app/Email');
        // Get all folders in the root folder
        $folders = File::directories($rootFolderPath);
        // Loop through each folder
        foreach ($folders as $folder) {
            // Get the folder name
            $folderName = basename($folder);
            $subFolders = File::directories($folder);           
            foreach ($subFolders as $subFolder){
                $subFolderName = basename($subFolder);

                // Specify the email address based on the folder name
                $destinationEmail = $this->getEmailForFolder($folderName, $subFolderName);
                $password = $subFolderName[0] . 'dole11' . strtolower($folderName) . date('mdy');
                \Log::info($password);
                // Get all files in the folder
                
                // Process the contents and send email
                $subject = $folderName . ' - ' . $subFolderName . ' - Email Verification';  // Replace with your desired subject
                $this->sendEmail($destinationEmail, $subject, $password);
            }
        }
    }    

    /**
     * Send an email.
     *
     * @param string $destinationEmail
     * @param string $subject
     * @param string $content
     */
    function sendEmail($destinationEmail, $subject, $password)
    {
    // Replace this with your email sending logic using Laravel's Mail facade
    // This is just a basic example, and you need to configure your email settings
    // in Laravel before using this in a production environment.

    $messageBody = 'Good day!' . PHP_EOL . PHP_EOL; // Concatenating strings with a new line
    $messageBody .= 'If you have received this email, you have successfully created the email in compliance with Memorandum No. ROXI-2024-FEBRUARY-067.' . PHP_EOL . PHP_EOL; // Concatenating another string with a new line
    $messageBody .= 'Please be informed that this message will be reiterated throughout the following days until we have verified that each Field Offices have correctly established their email accounts.' . PHP_EOL . PHP_EOL; // Concatenating another string with a new line
    $messageBody .= 'Thank you for your understanding.' . PHP_EOL . PHP_EOL . PHP_EOL . '-CASHIER UNIT';


    Mail::raw($messageBody, function ($message) use ($destinationEmail, $subject) {
        $message->to($destinationEmail)
                ->subject($subject);
    
    });
    }

    /**
     * Get the email address based on the folder name.
     *
     * @param string $folderName
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
                            break;
                    }
            case 'DIEO':
                switch ($subFolderName)
                    {
                        case 'GIP':
                            return 'gipremitdieodole11@gmail.com';
                        default:
                            break;
                    }
            default:
            return strtolower($subFolderName . 'remittance' . $folderName . 'dole11@gmail.com');
        }
    }
}
