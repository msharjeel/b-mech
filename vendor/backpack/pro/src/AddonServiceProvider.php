<?php

namespace Backpack\Pro;

use Illuminate\Support\ServiceProvider;

class AddonServiceProvider extends ServiceProvider
{
    use AutomaticServiceProvider;

    protected $vendorName = 'backpack';
    protected $packageName = 'pro';
    protected $commands = [];
}
