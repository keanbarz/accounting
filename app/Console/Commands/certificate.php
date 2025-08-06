<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class certificate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:certificate';

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
    {   $count = 1;
        // Specify the root folder path within the storage directory
        $rootFolderPath = storage_path('app/certificate');
        // Get all folders in the root folder
        $folders = File::directories($rootFolderPath);
        
        foreach ($folders as $folder) {
            // Get the folder name
            $folderName = basename($folder);

            $files = File::allFiles($folder);
            // Email and File Password Template
            $destinationEmail = $folderName . '@gmail.com';
            $subject = 'Certificate of Appearance Re: Orientation on Withholding Tax Computation';
            $this->info(sprintf("%02d", $count) . '. Sending email to ' . $destinationEmail . '...');
            $this->sendEmail($destinationEmail, $files, $subject);            

            // Delete files after sending; Increment Log Count
            File::delete($files);
            $count = $count + 1;
            File::delete($folder);
        }
    }    

    /*
     * Send an email.
     *
     * @param string $destinationEmail
     * @param string $subject
     * @param string $content
     */
    function sendEmail($destinationEmail, $files, $subject)
    {
        $mailData = [
            'subject' => $subject,
        ];

        Mail::send('certificate', $mailData, function ($message) use ($destinationEmail, $files, $subject) {
            $message->to($destinationEmail)->subject($subject);

            foreach ($files as $file) {
                // Attach each file to the email
                $message->attach($file);
            }
        });
    }
}
