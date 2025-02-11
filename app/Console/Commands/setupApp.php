<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class setupApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup python app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up Python environment...');
        
        // Check Python installation
        $pythonPath = trim(shell_exec('which python3 2>&1'));
        if (empty($pythonPath)) {
            $this->error('Python3 is not installed!');
            return 1;
        }
        
        $this->info("Python found at: {$pythonPath}");
        
        // Get Python version
        $version = trim(shell_exec('python3 --version 2>&1'));
        $this->info("Python version: {$version}");
        
        // Create virtual environment
        $venvPath = base_path('venv');
        if (!file_exists($venvPath)) {
            $this->info('Creating virtual environment...');
            
            // Check if pip is installed
            $pipCheck = trim(shell_exec('which pip3 2>&1'));
            if (empty($pipCheck)) {
                $this->info('Installing pip...');
                shell_exec('curl https://bootstrap.pypa.io/get-pip.py -o get-pip.py 2>&1');
                shell_exec('python3 get-pip.py 2>&1');
                unlink('get-pip.py');
            }
            
            shell_exec('python3 -m venv ' . $venvPath . ' 2>&1');
        }
        
        // Activate virtual environment and install packages
        $activatePath = $venvPath . '/bin/activate';
        $this->info('Installing required packages in virtual environment...');
        
        // Install onnxruntime and rembg
        $install = shell_exec("source {$activatePath} && pip3 install onnxruntime 'rembg=[cpu,cli]' 2>&1");
        $this->info($install);
        
        $this->info('Checking installations...');
        $packages = shell_exec("source {$activatePath} && pip3 list 2>&1");
        $this->info($packages);
        
        $this->info('Setup completed successfully!');
        return 0;
    }
}
