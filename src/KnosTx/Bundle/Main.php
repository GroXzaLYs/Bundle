<?php

declare(strict_types=1);

namespace KnosTx\Bundle;

use KnosTx\Bundle\item\Bundle;
use pocketmine\plugin\PluginBase;
use pocketmine\item\VanillaItems;

class Main extends PluginBase{
    public function onEnable(): void{
        ItemFactory::setup();
    }
}
