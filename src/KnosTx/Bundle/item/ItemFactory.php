<?php

declare(strict_types=1);

namespace KnosTx\Bundle\item;

use Closure;
use KnosTx\Bundle\registry\ObjectRegistry;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\ItemIdentifier as IID;

final class ItemFactory extends ObjectRegistry{

	protected static function registerDefaults() : void{
		self::register("Bundle", function(IID $id){
			return new Bundle($id);
		});
	}

	public static function register(string $name, Closure $createItem) : Item{
		$reflect = new \ReflectionClass(ItemTypeIds::class);
		$typeId = $reflect->getConstant(mb_strtoupper($name));

		if(!is_int($typeId)){
			\GlobalLogger::get()->warning(
				self::class . ": No ItemTypeId for $name, generating new one"
			);
			$typeId = ItemTypeIds::newId();
		}

		$item = $createItem(new IID($typeId));

		self::registerObject($name, $item);

		return $item;
	}
}
