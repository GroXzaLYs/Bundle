<?php

declare(strict_types=1);

namespace KnosTx\Bundle\item;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\SimpleInventory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

final class Bundle extends Item implements InventoryHolder{

	private const MAX_CAPACITY = 64;

	private SimpleInventory $inventory;

	public function __construct(ItemIdentifier $identifier){
		parent::__construct($identifier, "Bundle");
		$this->inventory = new SimpleInventory(1);
	}

	public function getMaxStackSize() : int{
		return 1;
	}

	public function getInventory() : SimpleInventory{
		return $this->inventory;
	}

	public function addItem(Item $item) : bool{
		if($item instanceof self){
			return false;
		}

		$used = 0;
		foreach($this->inventory->getContents() as $content){
			$used += $this->getWeight($content);
		}

		if($used + $this->getWeight($item) > self::MAX_CAPACITY){
			return false;
		}

		$this->inventory->addItem($item);
		return true;
	}

	private function getWeight(Item $item) : int{
		return $item->getMaxStackSize() === 1
			? self::MAX_CAPACITY
			: (int) ceil(self::MAX_CAPACITY / $item->getMaxStackSize());
	}

	protected function serializeCompoundTag(CompoundTag $nbt) : void{
		$list = [];

		foreach($this->inventory->getContents() as $item){
			$list[] = $item->nbtSerialize();
		}

		$nbt->setTag("BundleItems", new ListTag($list));
	}

	protected function deserializeCompoundTag(CompoundTag $nbt) : void{
		$list = $nbt->getListTag("BundleItems");
		if($list === null){
			return;
		}

		foreach($list as $tag){
			$this->inventory->addItem(Item::nbtDeserialize($tag));
		}
	}
}
