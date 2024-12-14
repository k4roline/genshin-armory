<?php

namespace App\Command;

use App\Service\ResourceDownloader;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractResourceCommand extends Command {
    private readonly Package $assets;

    public function __construct(private readonly ResourceDownloader $downloader) {
        $this->assets = new Package(new EmptyVersionStrategy());
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $array = explode(':', $this->getName());
        $name = $array[array_key_last($array)];
        $url = $_ENV['RESOURCE_' . strtoupper($name)];
        $path = $this->assets->getUrl('public/res/'.$name.'.json');
        if ($this->downloader->download($url, $path, $output)) {
            return Command::SUCCESS;
        }
        return Command::FAILURE;
    }
}