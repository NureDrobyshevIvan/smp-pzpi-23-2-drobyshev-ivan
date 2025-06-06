<?php

class Store {
    private array $products = [
        ['id' => 1, 'name' => 'Молоко пастеризоване', 'price' => 12],
        ['id' => 2, 'name' => 'Хліб чорний', 'price' => 9],
        ['id' => 3, 'name' => 'Сир білий', 'price' => 21],
        ['id' => 4, 'name' => 'Сметана 20%', 'price' => 25],
        ['id' => 5, 'name' => 'Кефір 1%', 'price' => 19],
        ['id' => 6, 'name' => 'Вода газована', 'price' => 18],
        ['id' => 7, 'name' => 'Печиво "Весна"', 'price' => 14]
    ];
    
    private array $cart = [];
    private string $username = '';
    private int $age = 0;
    
    private function getMaxNameLengthFromArray(array $items): int {
        $maxLength = 0;
        foreach ($items as $item) {
            preg_match_all('/./us', $item['name'], $smth);
            $length = count($smth[0]);
            if ($length > $maxLength) {
                $maxLength = $length;
            }
        }
        return $maxLength;
    }
    
    public function displayMenu(): void {
        echo "1 Вибрати товари\n";
        echo "2 Отримати підсумковий рахунок\n";
        echo "3 Налаштувати свій профіль\n";
        echo "0 Вийти з програми\n";
        echo "Введіть команду: ";
    }
    
    public function displayProducts(): void {
        $maxNameLength = $this->getMaxNameLengthFromArray($this->products);
        echo "№  НАЗВА" .  str_repeat(" ", $maxNameLength - 3) . "ЦІНА\n";
        foreach ($this->products as $product) {
            preg_match_all('/./us', $product['name'], $smth);
            $currentLength = count($smth[0]);
            $padding = str_repeat(" ", max(0, $maxNameLength - $currentLength + 2));
            printf("%-2d %s%s%d\n",
                $product['id'],
                $product['name'],
                $padding,
                $product['price']
            );
        }
        echo "   " . str_repeat("-", $maxNameLength + 8) . "\n";
        echo "0  ПОВЕРНУТИСЯ\n";
        echo "Виберіть товар: ";
    }
    
    public function addToCart(int $productId, int $quantity): bool {
        if ($quantity < 0 || $quantity > 100) {
            return false;
        }
        
        $product = array_filter($this->products, fn($p) => $p['id'] === $productId);
        if (empty($product)) {
            return false;
        }
        
        $product = reset($product);
        
        if ($quantity === 0) {
            unset($this->cart[$productId]);
            echo "ВИДАЛЯЮ З КОШИКА\n";
            if (empty($this->cart)) {
                echo "КОШИК ПОРОЖНІЙ\n";
            }
            return true;
        }
        
        $this->cart[$productId] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity
        ];
        
        $maxNameLength = $this->getMaxNameLengthFromArray($this->cart);
        echo "У КОШИКУ:\n";
        echo "НАЗВА" . str_repeat(" ", max(0, $maxNameLength - 3)) . "КІЛЬКІСТЬ\n";
        foreach ($this->cart as $item) {
            preg_match_all('/./us', $item['name'], $smth);
            $currentLength = count($smth[0]);
            $padding = str_repeat(" ", max(0, $maxNameLength - $currentLength + 2));
            printf("%s%s%d\n",
                $item['name'],
                $padding,
                $item['quantity']
            );
        }
        
        return true;
    }
    
    public function displayBill(): void {
        if (empty($this->cart)) {
            echo "КОШИК ПОРОЖНІЙ\n";
            return;
        }
        
        $maxNameLength = $this->getMaxNameLengthFromArray($this->cart);
        echo "№  НАЗВА" . str_repeat(" ", max(0, $maxNameLength - 3)) . "ЦІНА  КІЛЬКІСТЬ  ВАРТІСТЬ\n";
        $total = 0;
        $counter = 1;
        
        foreach ($this->cart as $item) {
            $cost = $item['price'] * $item['quantity'];
            $total += $cost;
            preg_match_all('/./us', $item['name'], $smth);
            $currentLength = count($smth[0]);
            $padding = str_repeat(" ", max(0, $maxNameLength - $currentLength + 2));
            printf("%-2d %s%s%-5d %-10d %d\n",
                $counter++, 
                $item['name'],
                $padding,
                $item['price'],
                $item['quantity'],
                $cost
            );
        }
        
        echo "РАЗОМ ДО CПЛАТИ: " . $total . "\n";
    }
    
    public function getProductById(int $id): ?array {
        foreach ($this->products as $product) {
            if ($product['id'] === $id) {
                return $product;
            }
        }
        return null;
    }

    public function setProfile(): void {
        do {
            $name = readline("Ваше ім'я: ");
            $validName = !empty($name) && preg_match('/^\p{L}+$/u', $name);
            if (!$validName) {
                echo "ПОМИЛКА! Ім'я повинно містити хоча б одну літеру та не повинно містити цифр\n";
            }
        } while (!$validName);
    
        do {
            $age = (int)readline("Ваш вік: ");
            $validAge = (is_numeric($age) && $age >= 7 && $age <= 150);
            if (!$validAge) {
                echo "ПОМИЛКА! Вік повинен бути від 7 до 150 років\n";
            }
        } while (!$validAge);
        $this->username = $name;
        $this->age = $age;
        echo "Ім'я: $name\n";
        echo "Вік: $age\n";
    }
}

$store = new Store();
echo "################################\n";
echo "# ПРОДОВОЛЬЧИЙ МАГАЗИН \"ВЕСНА\" #\n";
echo "################################\n";

while (true) {
    $store->displayMenu();
    $command = readline();
    
    switch ($command) {
        case '0':
            exit("Дякуємо за покупки!\n");
            
        case '1':
            while (true) {
                $store->displayProducts();
                $productId = (int)readline();
                
                if ($productId === 0) {
                    break;
                }
                
                $product = $store->getProductById($productId);
                if (!$product) {
                    echo "ПОМИЛКА! ВКАЗАНО НЕПРАВИЛЬНИЙ НОМЕР ТОВАРУ\n";
                    continue;
                }
                
                echo "Вибрано: " . $product['name'] . "\n";
                $quantity = (int)readline("Введіть кільксть штук: ");
                
                $store->addToCart($productId, $quantity);
            }
            break;
            
        case '2':
            $store->displayBill();
            break;
            
        case '3':
            $store->setProfile();
            break;
            
        default:
            echo "ПОМИЛКА! Введіть правильну команду\n";
    }
} 