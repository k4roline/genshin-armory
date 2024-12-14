<?php

namespace App\Command;

use App\Service\ResourceDownloader;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:import:namecard',
    description: 'Downloads namecard data from external source'
)]
class ImportNamecardCommandAbstract extends AbstractResourceCommand {
    public function __construct(ResourceDownloader $downloader) {
        parent::__construct($downloader);
    }
}