<?php

class ElectronicItems
{
    private $items = [];
    
    public function __construct(array $items)
    {
        $this->items = $items;
    }
 
    /**
     * Return a sorted list of items by price
     *
     * @author Okiemute Omuta <iamkheme@gmail.com>
     *
     * @return array
     */
    public function getSortedItems() : array
    {
        $sorted = [];
        
        foreach ($this->items as $key => $item) {
            $sorted[number_format($item->getPriceOnly(), 2) . str_repeat(0, $key)] = $item;
        }
        
        ksort($sorted);

        return $sorted;
    }
    
    /**
     * Return a list of items that match the type provided
     *
     * @param string $item_type Type of item of interest
     *
     * @author Okiemute Omuta <iamkheme@gmail.com>
     *
     * @return array
     */
    public function getItemsByType(string $item_type) : array
    {
        if (in_array($item_type, ElectronicItem::$types)) {
            return array_filter($this->items, function ($item) use ($item_type) {
                return $item->getType() == $item_type;
            });
        }
    
        return [];
    }

    /**
     * Return a count of the number of items 
     *
     * @author Okiemute Omuta <iamkheme@gmail.com>
     *
     * @return int
     */
    public function getItemCount() : int
    {
        return count($this->items);
    }

    /**
     * Return a sum of total prices for all items
     *
     * @author Okiemute Omuta <iamkheme@gmail.com>
     *
     * @return float
     */
    public function getPrice() : float
    {
        return array_sum(array_keys($this->getSortedItems()));
    }
}

class ElectronicItem
{
    /**
     * @var float
     */
    public $price;
    public $is_wired = false;
    
    /**
     * @var string
     */
    private $type;
    private $max_extras = -1;
    private $extras;
    
    const ELECTRONIC_ITEM_TELEVISION = 'television';
    const ELECTRONIC_ITEM_CONSOLE    = 'console';
    const ELECTRONIC_ITEM_MICROWAVE  = 'microwave';
    const ELECTRONIC_ITEM_CONTROLLER = 'controller';
    
    public static $types = [
        self::ELECTRONIC_ITEM_CONSOLE,
        self::ELECTRONIC_ITEM_MICROWAVE,
        self::ELECTRONIC_ITEM_TELEVISION,
        self::ELECTRONIC_ITEM_CONTROLLER
    ];
    
    /**
     * Return the price of the item
     *
     * @author Okiemute Omuta <iamkheme@gmail.com>
     *
     * @return float
     */
    public function getPriceOnly() : float
    {
        return $this->price;
    }
    
    /**
     * Return the type of the item
     *
     * @author Okiemute Omuta <iamkheme@gmail.com>
     *
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }
    
    /**
     * Return true if the item is wired, otherwise, false.
     *
     * @author Okiemute Omuta <iamkheme@gmail.com>
     *
     * @return bool
     */
    public function getWired() : bool
    {
        return $this->is_wired;
    }
    
    /**
     * Set the price item's price
     *
     * @param float $item_price Price for the item
     *
     * @author Okiemute Omuta <iamkheme@gmail.com>
     *
     * @return void
     */
    public function setPrice(float $item_price) : void
    {
        $this->price = $item_price;
    }
    
    /**
     * Set the type for the item
     *
     * @param string $item_type String identifier for the item type
     *
     * @author Okiemute Omuta <iamkheme@gmail.com>
     *
     * @return void
     */
    public function setType(string $item_type) : void
    {
        $this->type = $item_type;
    }
    
    /**
     * Set an item as wired. Items are wireless by default.
     *
     * @author Okiemute Omuta <iamkheme@gmail.com>
     *
     * @return void
     */
    public function setWired() : void
    {
        $this->is_wired = true;
    }

    /**
     * Add extras to an item to complement the item
     *
     * @param array $list Array containing list of item to add as extras
     *
     * @author Okiemute Omuta <iamkheme@gmail.com>
     *
     * @return void
     */
    public function addExtras(array $list) : void
    {
        $item_extras = new ElectronicItems($list);

        if ($item_extras->getItemCount() > $this->max_extras && $this->max_extras != -1) {
            throw new UnexpectedValueException("Max extras ({$this->max_extras}) exceeded");
        }

        $this->extras = $item_extras;
    }

    /**
     * Return a list of extras attached to this item
     *
     * @author Okiemute Omuta <iamkheme@gmail.com>
     *
     * @return ElectronicItems
     */
    public function getExtras() : ElectronicItems
    {
        return $this->extras;
    }

    /**
     * Set the maximum number of extras that can be attached to an item
     *
     * @param int $max Maximum number of extras allowed
     *
     * @author Okiemute Omuta <iamkheme@gmail.com>
     *
     * @return void
     */
    public function maxExtras(int $max) : void
    {
        $this->max_extras = $max;
    }

    public function getPriceWithExtras()
    {
        return $this->getPriceOnly() + $this->getExtras()->getPrice();
    }
}

$electronic_item = new ElectronicItem();

$wireless_controller = clone $electronic_item;
$wireless_controller->setType('controller');
$wireless_controller->maxExtras(0);
$wireless_controller->setPrice(2.49);

$wired_controller = clone $wireless_controller;
$wired_controller->setWired();
$wired_controller->setPrice(2.0);

$console = clone $electronic_item;
$console->setType('console');
$console->setPrice(9.99);
$console->maxExtras(4);
$console->addExtras([ $wireless_controller, $wireless_controller, $wired_controller, $wired_controller ]);

$television_1 = clone $electronic_item;
$television_1->setType('television');

$television_2 = clone $television_1;

$television_1->setPrice(29.99);
$television_1->addExtras([ $wireless_controller, $wireless_controller ]);

$television_2->setPrice(39.99);
$television_2->addExtras([ $wireless_controller ]);

$microwave = clone $electronic_item;
$microwave->setType('microwave');
$console->maxExtras(0);
$microwave->setPrice(20.99);

$purchases = new ElectronicItems([ $console, $television_1, $television_2, $microwave ]);

print_r($purchases->getSortedItems());
print_r("\n\nTotal Pricing: {$purchases->getPrice()}");
print_r("\n\nConsole Total Price: {$console->getPriceWithExtras()}");