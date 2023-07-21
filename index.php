<?php

require __DIR__.'/src/MinecraftPing.php';
require __DIR__.'/src/MinecraftPingException.php';
require __DIR__.'/src/MinecraftQuery.php';
require __DIR__.'/src/MinecraftQueryException.php';

use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;
use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;

const MQ_SERVER_ADDR = '127.0.0.1';
const MQ_SERVER_PORT = 25565;
const MQ_SERVER_QUERY_PORT = 25565;
const MQ_TIMEOUT = 1;

$timer = microtime(true);

$info = false;
$query = null;

try {
    $query = new MinecraftPing(MQ_SERVER_ADDR, MQ_SERVER_PORT, MQ_TIMEOUT);

    $info = $query->Query();

    if ($info === false) {
        $query->Close();
        $query->Connect();

        $info = $query->QueryOldPre17();
    }
} catch (MinecraftPingException $e) {
    $Exception = $e;
}

if ($query !== null) {
    $query->Close();
}

$queryInfo = null;
$players = [];
$query = new MinecraftQuery();

try {
    $query->Connect(MQ_SERVER_ADDR, MQ_SERVER_QUERY_PORT);

    $queryInfo = $query->GetInfo();
    $players = $query->GetPlayers();
    if ($players === false) {
        $players = [];
    }
} catch (MinecraftQueryException $e) {
    echo $e->getMessage();
}

$timer = number_format(microtime(true) - $timer, 4, '.', '');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/favicon.ico"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/dist/output.css" rel="stylesheet">
    <title>我的世界</title>
</head>
<body class="bg-gradient-to-r from-sky-500 to-indigo-500">
<div class="flex justify-center items-center h-screen">
    <div class="p-6 mx-auto">
        <div class="flex justify-center space-x-4" style="align-items: center;">
            <?php
            echo '<img src="'.str_replace("\n", "",
                    $info['favicon']).'" class="rounded" style="width:64px;height:64px;">';
            ?>
            <div>
                <h1 class="text-3xl font-bold">
                    <?= $queryInfo['GameName']; ?>
                </h1>
                <h2 class="text"><?= $queryInfo['HostName']; ?></h2>
            </div>
        </div>

        <div style="margin-top: 2rem">
            版本：<?= $queryInfo['Version']; ?> <?= empty($queryInfo['Plugins']) ? '原版服务器' : 'Mod 服务器' ?> <?= $queryInfo['Software'] ?>
        </div>
        <div>单人｜多人：<?= $queryInfo['GameType'] === 'SMP' ? '多人游戏' : '单人游戏' ?> </div>

        <div style="margin-top: 2rem">
            在线人数：<?= $queryInfo['Players'] ?> / <?= $queryInfo['MaxPlayers'] ?>
        </div>
        <?php if (count($players) >= 1): ?>
            <div>
                <div>在线玩家：</div>
                <div class="flex content-center space-x-2">
                    <div class="flex space-x-1 justify-center" style='align-self: center; flex-wrap: wrap;'>
                        <?php
                        foreach ($players as $player) {
                            echo "<div class='flex flex-col space-x-1'>";
                            echo "<img src='https://minotar.net/cube/$player/64.png' style='width:2rem; height:2rem; align-self: center;'>";
                            echo "<div>$player</div>";
                            echo "</div>";
                        } ?>
                    </div>
                </div>
                <div style="border-bottom: 3rem solid;border-image: url(../images/minecraft_grass_block_texture.jpg) 1280 0 repeat;margin-top: 2rem">
                    <div class="flex" style='align-self: center;'>
                        <?php
                        foreach ($players as $player) {
                            echo "<img src='https://minotar.net/body/$player/64.png' style='width:auto; height:6rem; margin-right:0.5rem'>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="text" style="margin-top: 2rem">
            服务器查询用时 <?= $timer ?> 秒
        </div>
    </div>
</div>
</body>
</html>

