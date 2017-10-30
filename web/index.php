<?php

require_once __DIR__.'/../vendor/autoload.php';

use TRTApi\TRTApi;
use Dashboard\Components\BalanceCalculator;
use Dashboard\Entities\Trade;
use Dashboard\Entities\OrderBook;


$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app['debug'] = true;

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig', [
    ]);
});


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$app->get('/balance/{apiKey}/{apiSecret}/{fundIds}', function($apiKey, $apiSecret, $fundIds) use($app) {

    $trtApi = new TRTApi($apiKey, $apiSecret);

    $funds = explode(',', $fundIds);


    if(!count($funds)) {
       throw new \Exception('No funds selected');
    }

    $bcs = [];
    $elabTrades = [];
    $elabBalances = [];
    $tickers = [];

    foreach($funds as $fundId) {
        $trades = $trtApi->getTrades($fundId);
        $bcs[$fundId] = new BalanceCalculator();
        $bc = $bcs[$fundId];

        if(!isset($trades['trades'])) {
           throw new \Exception('The API did not return the expected result');
        }

        foreach ($trades['trades'] as $t) {
            $tmpTrade = new Trade($t);
            $bc->addTrade($tmpTrade);
        }

        $tickers[$fundId] = $trtApi->getTicker($fundId);
        $bc->setCurrentPrice($tickers[$fundId]['last']);

        $result[$fundId] = array(
            'last' => $tickers[$fundId]['last'],
            'balances' => $bc->getBalances(),
            'finalBalance' => $bc->getFinalBalance()
        );

        $elabTrades[$fundId] = $bc->getTrades(Trade::SIDE_BUY);
        $elabBalances[$fundId] = $bc->getBalances();

    }



    $params =  [
        'tickers' => $tickers,
        'funds' => $funds,
        'trades' => $elabTrades,
        'balances'=> $elabBalances,
        'balanceCalculators'=>$bcs
    ];

    return $app['twig']->render('balance.twig', $params);

    return $app->json($result);
});


$app->get('/position/{tradeId}/{apiKey}/{apiSecret}', function($tradeId, $apiKey, $apiSecret) use($app) {

    $trtApi = new TRTApi($apiKey, $apiSecret);

    $trades = $trtApi->getTrades();
    $bc = new BalanceCalculator();

    $trade = false;
    foreach ($trades['trades'] as $t) {
        if($t['id'] == $tradeId) {
            $trade = new Trade($t);
        }

    }

    if($trade) {

        $tradeQty = $trade->getAmount();
        $orderBook = new OrderBook($trtApi->getOrderBook());
        $ticker = $trtApi->getTicker();
        $lastSell = $ticker['last'];
        $topBuy = $orderBook->getTopBuy($tradeQty);

        $instaSell = $bc->calculateInstantProfit($trade, $topBuy);
        $currentPriceSell = $bc->calculateInstantProfit($trade, $lastSell);

        $result = array(
            'trade' => [
                'amount' => $trade->getAmount(),
                'price' =>  $trade->getPrice(),
                'value' => $trade->getAmount() * $trade->getPrice(),
                'buying_fee' => $trade->calculateFee()
            ],
            'sell_last' => [
                'price'=> $lastSell,
                'value' => $trade->getAmount() * $lastSell,
                'selling_fee' => $lastSell * $trade->getAmount() * 0.002,
                'profit'=> $currentPriceSell
            ],
            'instaSell' => [
                'price'=> $topBuy,
                'value' => $trade->getAmount() * $topBuy,
                'selling_fee'=> $topBuy * $trade->getAmount() * 0.002,
                'profit'=> $instaSell
            ],
            'last' => $ticker['last']

        );

        return $app->json($result);
    }

    throw new \Exception("Something bad happened");


});

$app->run();
