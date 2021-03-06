<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//how it should be able to be used

require_once './SynkaTester.php';

$local=new PDO('mysql:host=localhost:3306;dbname=fundtracker;charset=utf8', 'fundtracker', 'blomma22');
$local->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//test local more manual
$remote=new PDO('mysql:host=localhost:3306;dbname=fundtrackertest;charset=utf8', 'fundtracker', 'blomma22');
$remote->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$synka=new SynkaTester($local,$remote);

$synka->table("portfolios","id");
$synka->table("funds","id")->sync(["*-","portfolioId"],"id",">");
$synka->table("fundratings","id")->sync("*","id",">");
$synka->table("portfolio_snapshots","id")->sync("*","id",">");
$synka->table("portfolio_snapshots_in_portfolios","id")->sync("*","id",">");
$synka->table("exchanges","exchangeName")->sync("*","exchangeName","!=");
$synka->table("tickers","tickerSymbol")->sync("*","tickerSymbol","!=");
$synka->table("securities","id")->sync("*","id",">")->syncUpd(["notes","ignored","tickerId"],"updatedAt");
$synka->table("portfoliorows")->sync("*","portfolioSnapshotId",">");
$synka->table("strategies","id")->sync("*","id","!=")->syncUpd("*","updatedOn");
$synka->table("dividends")->sync("*","time1",">","tickerId")->sync("*","tickerId","!=");
$synka->table("splits")->sync("*","time1",">","tickerId")->sync("*","tickerId","!=");
$synka->table("quotes")->sync("*","date",">","tickerId")->sync("*","tickerId","!=");;
$synka->table("tickers_in_strategies")->sync("*",["strategyId","tickerId"],"!=")->syncUpd(["included"],"updatedAt");
$syncData=$synka->analyze(false);//optional, if not called explicitly then Synka->sync() will call it
$synka->commit();

echo $synka->checkForDiscrepancies()?"match":"mismatch";