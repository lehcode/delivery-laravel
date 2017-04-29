<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 14:53
 */

namespace App\Extensions;

/**
 * Class SeedCommand
 * @package App\Extensions
 */
class SeedCommand extends \Illuminate\Database\Console\Seeds\SeedCommand {
    /**
     *
     */
    public function fire()
    {
        if (! $this->confirmToProceed())
            return;

        $this->resolver->setDefaultConnection($this->getDatabase());
        $this->getSeeder()->run();
    }
}
