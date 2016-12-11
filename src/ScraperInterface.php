<?php

namespace App;

interface ScraperInterface {

    public function scrapePage($page, $aggregates);

}