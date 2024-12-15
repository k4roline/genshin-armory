<?php

namespace App\Command;

use App\Service\ResourceDownloader;
use Symfony\Component\Asset\Package;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:import:pfp',
    description: 'Downloads character profile picture data from external source'
)]
class ImportPfpCommandAbstract extends AbstractResourceCommand {
    public function __construct(ResourceDownloader $downloader) {
        parent::__construct($downloader);
    }
}