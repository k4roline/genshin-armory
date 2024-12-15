<?php

namespace App\Service;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class NamecardCollector {
    private Package $assets;
    private Filesystem $filesystem;
    private string $extension;
    public function __construct(private HttpClientInterface $httpClient) {
        $this->assets = new Package(new EmptyVersionStrategy());
        $this->filesystem = new Filesystem();
        $this->extension = '.png';
    }

    /**
     * Checks if a namecard with given id exists. If so, returns its path, otherwise calls collect()
     *
     * @param int $id id of the namecard
     * @return string path to the namecard
     *
     * @see NamecardCollector::collect()
     */
    public function getNamecard(int $id): string {
        if ($this->filesystem->exists($file = $this->assets->getUrl('images/namecard/'.$id.$this->extension))) {
            return $file;
        }
        return $this->collect($id);
    }

    /**
     * Reads namecard.json file for a namecard with given id, and fetches it from external API
     *
     * @param int $id id of the namecard to fetch
     * @return string relative path to the imported namecard
     */
    public function collect(int $id): string {
        $file = $this->filesystem->readFile($this->assets->getUrl('res/namecard.json'));
        $json = json_decode($file, true);
        if ($namecard = $json[$id]['icon']) {
            try {
                $response = $this->httpClient->request('GET', 'https://enka.network/ui/' . $namecard . $this->extension);
                $this->filesystem->dumpFile($this->assets->getUrl('images/namecard/'.$id . $this->extension), $response->getContent());
            } catch (ClientExceptionInterface
                    |TransportExceptionInterface
                    |ServerExceptionInterface
                    |RedirectionExceptionInterface $e) {
                // todo fill
            }
        }
        return $this->assets->getUrl('images/namecard/'.$id . $this->extension);
    }
}
