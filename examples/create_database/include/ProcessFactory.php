<?php

class ProcessFactory implements \Etouches\Phplumber\ProcessFactoryInterface
{
    /**
     * Create a new process object based on its name.
     *
     * This is our chance to inject any dependencies into our process objects
     * (e.g. database connections, config options, etc.).
     *
     * @param string $processName
     * @return \Etouches\Phplumber\ProcessInterface
     */
    public function make($processName)
    {
        $process = null;
        switch ($processName) {
            case 'CreateDatabase':
                $process = new CreateDatabase();
                break;
            case 'CreateTable':
                $process = new CreateTable();
                break;
            case 'CreateViews':
                $process = new CreateViews();
                break;
        }

        return $process;
    }
}
