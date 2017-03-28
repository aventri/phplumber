<?php

class CreateAndFillDatabase extends \Etouches\Phplumber\ProcessList
{
    protected function setup()
    {
        $this
            ->add('CreateDatabase')
            ->add('CreateTable')
            ->add('CreateViews');
    }
}
