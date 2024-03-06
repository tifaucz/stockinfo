<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StockPriceService
{
    public function exchangeList(){ // info from https://docs.google.com/spreadsheets/d/1I3pBxjfXB056-g_JYf_6o3Rns3BV2kMGG1nCatb91ls/edit#gid=0
        return [
            // 'AD' => 'ABU DHABI SECURITIES EXCHANGE',
            // 'AQ' => 'Aquis Exchange',
            // 'AS' => 'NYSE EURONEXT - EURONEXT AMSTERDAM',
            // 'AT' => 'ATHENS EXCHANGE S.A. CASH MARKET',
            // 'AX' => 'ASX - ALL MARKETS',
            // 'BA' => 'BOLSA DE COMERCIO DE BUENOS AIRES',
            // 'BC' => 'BOLSA DE VALORES DE COLOMBIA',
            // 'BD' => 'BUDAPEST STOCK EXCHANGE',
            // 'BE' => 'BOERSE BERLIN',
            // 'BH' => 'BAHRAIN BOURSE',
            // 'BK' => 'STOCK EXCHANGE OF THAILAND',
            // 'BO' => 'BSE LTD',
            // 'BR' => 'NYSE EURONEXT - EURONEXT BRUSSELS',
            // 'CA' => 'Egyptian Stock Exchange',
            // 'CN' => 'CANADIAN NATIONAL STOCK EXCHANGE',
            // 'CO' => 'OMX NORDIC EXCHANGE COPENHAGEN A/S',
            // 'CR' => 'CARACAS STOCK EXCHANGE',
            // 'CS' => 'CASABLANCA STOCK EXCHANGE',
            // 'DB' => 'DUBAI FINANCIAL MARKET',
            // 'DE' => 'XETRA',
            // 'DS' => 'Dhaka Stock Exchange',
            // 'DU' => 'BOERSE DUESSELDORF',
            // 'F'  => 'DEUTSCHE BOERSE AG',
            // 'HE' => 'NASDAQ OMX HELSINKI LTD',
            // 'HK' => 'HONG KONG EXCHANGES AND CLEARING LTD',
            // 'HM' => 'HANSEATISCHE WERTPAPIERBOERSE HAMBURG',
            // 'IC' => 'NASDAQ OMX ICELAND',
            // 'IR' => 'IRISH STOCK EXCHANGE - ALL MARKET',
            // 'IS' => 'BORSA ISTANBUL',
            // 'JK' => 'INDONESIA STOCK EXCHANGE',
            // 'JO' => 'JOHANNESBURG STOCK EXCHANGE',
            // 'KL' => 'BURSA MALAYSIA',
            // 'KQ' => 'KOREA EXCHANGE (KOSDAQ)',
            // 'KS' => 'KOREA EXCHANGE (STOCK MARKET)',
            // 'KW' => 'Kuwait Stock Exchange',
            // 'L'  => 'LONDON STOCK EXCHANGE',
            // 'LN' => 'Euronext London',
            // 'LS' => 'NYSE EURONEXT - EURONEXT LISBON',
            // 'MC' => 'BOLSA DE MADRID',
            // 'ME' => 'MOSCOW EXCHANGE',
            // 'MI' => 'Italian Stock Exchange',
            // 'MU' => 'BOERSE MUENCHEN',
            // 'MX' => 'BOLSA MEXICANA DE VALORES (MEXICAN STOCK EXCHANGE)',
            // 'NE' => 'AEQUITAS NEO EXCHANGE',
            // 'NL' => 'Nigerian Stock Exchange',
            // 'NS' => 'NATIONAL STOCK EXCHANGE OF INDIA',
            // 'NZ' => 'NEW ZEALAND EXCHANGE LTD',
            // 'OL' => 'OSLO BORS ASA',
            // 'PA' => 'NYSE EURONEXT - MARCHE LIBRE PARIS',
            // 'PM' => 'Philippine Stock Exchange',
            // 'PR' => 'PRAGUE STOCK EXCHANGE',
            // 'QA' => 'QATAR EXCHANGE',
            // 'RG' => 'NASDAQ OMX RIGA',
            // 'SA' => 'Brazil Bolsa - Sao Paolo',
            // 'SG' => 'BOERSE STUTTGART',
            // 'SI' => 'SINGAPORE EXCHANGE',
            // 'SN' => 'SANTIAGO STOCK EXCHANGE',
            // 'SR' => 'SAUDI STOCK EXCHANGE',
            // 'SS' => 'SHANGHAI STOCK EXCHANGE',
            // 'ST' => 'NASDAQ OMX NORDIC STOCKHOLM',
            // 'SW' => 'SWISS EXCHANGE',
            // 'SZ' => 'SHENZHEN STOCK EXCHANGE',
            // 'T'  => 'TOKYO STOCK EXCHANGE-TOKYO PRO MARKET',
            // 'TA' => 'TEL AVIV STOCK EXCHANGE',
            // 'TL' => 'NASDAQ OMX TALLINN',
            // 'TO' => 'TORONTO STOCK EXCHANGE',
            // 'TW' => 'TAIWAN STOCK EXCHANGE',
            // 'TWO'=> 'TPEx',
            // 'TU' => 'Turquoise',
            'US' => 'US exchanges (NYSE, Nasdaq)',
            // 'V'  => 'TSX VENTURE EXCHANGE - NEX',
            // 'VI' => 'Vienna Stock Exchange',
            // 'VN' => 'Vietnam exchanges including HOSE, HNX and UPCOM',
            // 'VS' => 'NASDAQ OMX VILNIUS',
            // 'WA' => 'WARSAW STOCK EXCHANGE/EQUITIES/MAIN MARKET',
            // 'XA' => 'CBOE Australia',
            // 'HA' => 'Hanover Stock Exchange',
            // 'SX' => 'DEUTSCHE BOERSE Stoxx',
            // 'TG' => 'DEUTSCHE BOERSE TradeGate',
            // 'SC' => 'BOERSE_FRANKFURT_ZERTIFIKATE',
            // 'SL' => 'Spotlight Stock Market',
        ];    
    }

    public function getRealTimePrice($symbol)
    {
        $apiKey = env('FINNHUB_API_KEY');
        $response = Http::withHeaders([
            'X-Finnhub-Token' => $apiKey
        ])->get("https://finnhub.io/api/v1/quote", [
            'symbol' => $symbol
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            return $response->json(['error' => 'Unable to fetch stock price'], 500);
        }
    }

    public function getSymbols($exchange = 'US', $mic = false)
    {
        $data = [
            'exchange' => $exchange
        ];
        if ($mic) {
            $data['mic'] = $mic;
        }

        $apiKey = env('FINNHUB_API_KEY');
        $response = Http::withHeaders([
            'X-Finnhub-Token' => $apiKey
        ])->get("https://finnhub.io/api/v1/stock/symbol", $data);

        if ($response->successful()) {
            // Log::info('getSymbols: ' . print_r($response->json(), true));
            $symbolsData = $response->json();

            return collect($symbolsData)->sortBy('symbol')->mapWithKeys(function ($item) {
                return [
                    $item['symbol'] => (object)[
                        'description' => $item['description'],
                        'mic' => $item['mic'],
                        'type' => $item['type'],
                    ],
                ];
            })->toArray();
        }

        return false;
    }
}