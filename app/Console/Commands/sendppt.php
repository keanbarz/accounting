<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class sendppt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sendppt';

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
        $files = File::allFiles($rootFolderPath);
        
            $subject = 'Presentation Files Re: Orientation on Withholding Tax Computation';
            $this->info(sprintf("%02d", $count) . '. Sending email to everyone...');
            $this->sendEmail($files, $subject, $folders);            

            // Delete files after sending; Increment Log Count
            File::delete($files);
            $count = $count + 1;
            File::delete($folders);
        
    }    

    /*
     * Send an email.
     *
     * @param string $destinationEmail
     * @param string $subject
     * @param string $content
     */
    function sendEmail($files, $subject, $folders)
    {
        $mailData = [
            'subject' => $subject,
        ];

        Mail::send('ppt', $mailData, function ($message) use ($files, $subject, $folders) {
            $message->to('dolexicashier@gmail.com')->subject($subject);
            foreach ($folders as $folder) {
                $folderName = basename($folder);

                $message->cc($folderName . '@gmail.com');
            }
            foreach ($files as $file) {
                // Attach each file to the email
                $message->attach($file);
            }
        });
    }
}
