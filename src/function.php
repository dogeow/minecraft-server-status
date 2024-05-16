<?php

use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;
use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;

function createPing($serverAddr, $serverPort, $timeout)
{
    try {
        $query = new MinecraftPing($serverAddr, $serverPort, $timeout);
        $info = $query->Query();

        if ($info === false) {
            $query->Close();
            $query->Connect();
            $info = $query->QueryOldPre17();
        }

        return $info;
    } catch (MinecraftPingException $e) {
        echo "Unable to ping Minecraft server: ".$e->getMessage();
        exit;
    } finally {
        if ($query !== null) {
            $query->Close();
        }
    }
}

function createQuery($serverAddr, $queryPort)
{
    try {
        $query = new MinecraftQuery();
        $query->Connect($serverAddr, $queryPort);

        $queryInfo = $query->GetInfo();
        $players = $query->GetPlayers();
        if ($players === false) {
            $players = [];
        }

        return [$queryInfo, $players];
    } catch (MinecraftQueryException $e) {
        echo "Unable to query Minecraft server: ".$e->getMessage();
        exit;
    }
}