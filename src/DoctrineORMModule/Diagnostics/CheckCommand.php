<?php

namespace DoctrineORMModule\Diagnostics;

use DoctrineModule\Component\Console\Output\PropertyOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZendDiagnostics\Check\AbstractCheck;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;

class CheckCommand extends AbstractCheck
{
    /** @var Command */
    private $command;
    
    /** @var InputInterface */
    private $input;
    
    /** @var OutputInterface */
    private $output;

    /**
     * CheckSchema constructor.
     * @param Command $command
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(Command $command, InputInterface $input, OutputInterface $output)
    {
        $this->command = $command;
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * Perform the actual check and return a ResultInterface
     *
     * @return ResultInterface
     * @throws \Exception
     */
    public function check()
    {
        $exitCode = $this->command->run($this->input, $this->output);
        
        $data = [];
        if ($this->output instanceof PropertyOutput) {
            $data = $this->output->getMessage();
            if (!is_array($data)) {
                $data = explode(PHP_EOL, trim($data));
            }
        }
        
        if ($exitCode < 1) {
            return new Success(get_class($this->command), $data);
        }
        
        return new Failure(get_class($this->command), $data);
    }
}
