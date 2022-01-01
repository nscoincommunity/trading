<?php

include_once "vendor/autoload.php";
include_once "classes/Helper.php";
include_once "classes/Trade.php";
include_once "classes/User.php";
include_once "classes/Connection.php";
include_once "classes/Exchange.php";
include_once "classes/Database.php";
include_once "classes/Command.php";

//{"symbol":{{ticker}},"price":{{close}},"time":{{time}},"amount":10,"command":"sell_market"}

$tracker = "tracker.th";
$exchange_class = "\\ccxt\\binance";

$exchange = new $exchange_class ();
$command = Command::getInstance();
//$command->save_trade(array( "id"=>13,"ticker" => "XRP/USDT", "price" => 0.30, "amount" => 600, "command" => "sell_limit", "userId" => 1, "enabled" => 1, "exchangeId" => 1));
//$command->save_user(array("username"=>"kamal","password"=>"jamali"));

//$command->save_exchange(array("apiKey"=>"kamal","apiSecret"=>"jamali","userId"=>"jamali","exchange"=>"jamali"));
//die();
if (file_exists($tracker)) {
    $new_trades = file_get_contents($tracker);
} else {
    $new_trades = Helper::rand(20);
    file_put_contents($tracker, $new_trades);
}
$trades = $command->trades();
while (true) {
    $old_trades = file_get_contents($tracker);
    if ($new_trades != $old_trades) {
        $new_trades = $old_trades;

        echo "fetching new trades ...\n";
    }
    foreach ($trades as $trade) {
        $ticker = $exchange->fetch_ticker($trade->getTicker());
        echo $ticker["close"] . "\n";
        if ($ticker["close"] == $trade->getPrice()) {
//            $command->execute_trade($trade);
            var_dump($trade);
            $command->cancel_trade($trade->getId());
        }
    }
    sleep(2);
}


